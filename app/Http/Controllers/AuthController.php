<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    public function attempt(Request $request)
    {
        if ($request->filled(['username', 'password'])) {
            $username = $request->input('username');

            // Check if user exists locally
            $user = User::where('user_login', $username)
                        ->orWhere('email', $username)
                        ->first();

            if ($user) {
                return $this->localLogin($request);
            } else {
                return $this->remoteLogin($request);
            }
        }

        throw ValidationException::withMessages([
            'message' => 'Username and password are required',
        ]);
    }

    public function localLogin(Request $request)
    {
        $username = $request->input('username') ?? '';
        $password = $request->input('password') ?? '';

        // Try to authenticate using either user_login or email
        if (Auth::attempt(['user_login' => $username, 'password' => $password]) ||
            Auth::attempt(['email' => $username, 'password' => $password])) {
            // return response()->json(['message' => 'Login successful']);
        }

        throw ValidationException::withMessages([
            'message' => 'The credentials you entered are incorrect',
        ]);
    }

    public function remoteLogin(Request $request)
    {
        $username = $request->input('username') ?? '';
        $password = $request->input('password') ?? '';

        try {
            $response = Http::post('https://hivessel.com/wp-json/hivessel-api/v1/user', [
                'username' => $username,
                'password' => $password
            ]);

            if ($response->successful()) {
                $user = $response->json();

                $created = User::firstOrCreate(
                    ['email' => $user['user_email']],
                    [
                        'user_id'     => $user['ID'],
                        'name'        => $user['display_name'],
                        'user_login'  => $user['user_login'],
                        'email'       => $user['user_email'],
                        'password'    => bcrypt($password), // Note: consider storing flag for external user
                    ]
                );

                Auth::login($created);

                // return response()->json(['message' => 'Login successful']);
            }

        } catch (\Throwable $e) {
            info($e->getMessage());
        }

        throw ValidationException::withMessages([
            'message' => 'The credentials you entered are incorrect',
        ]);
    }
}
