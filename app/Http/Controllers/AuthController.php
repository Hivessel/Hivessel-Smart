<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    public function attempt($user, $password){
        return $user . ' ' . $password;
    }

    public function remoteLogin(Request $request){
        $username = $request->input('username') ?? '';
        $password = $request->input('password') ?? '';
        
        try{
            $response = Http::post('https://hivessel.com/wp-json/hivessel-api/v1/user', ['username' => $username, 'password' => $password]);
            if($response->successful()){
                $user = $response->json();
                try{
                    $created = User::firstOrCreate(['email' => $user['user_email']],[
                        'user_id' => $user['ID'],
                        'name' => $user['display_name'],
                        'user_login' => $user['user_login'],
                        'email' => $user['user_email'],
                        'password' => bcrypt($password),
                    ]);
                    Auth::login($created);

                }catch(\Throwable $e){
                    info($e->getMessage());
                    throw ValidationException::withMessages([
                        'message' => 'The credentials you entered is incorrect',
                    ]);
                    // return redirect()->route('login')->with(['message' => 'The credentials you entered is incorrect']);
                }
            }else{
                throw ValidationException::withMessages([
                    'message' => 'The credentials you entered is incorrect',
                ]);
                // return redirect()->route('login')->with(['message' => 'The credentials you entered is incorrect']);
            }
        }catch(\Throwable $e){
            info($e->getMessage());
            throw ValidationException::withMessages([
                'message' => 'The credentials you entered is incorrect',
            ]);
            // return redirect()->route('login')->with(['message' => 'The credentials you entered is incorrect']);
        }
    }
}
