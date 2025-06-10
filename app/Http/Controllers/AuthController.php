<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function attempt($user, $password){
        return $user . ' ' . $password;
    }
}
