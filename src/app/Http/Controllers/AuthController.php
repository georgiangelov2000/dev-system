<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

class AuthController extends Controller {

    public function getLogin() {
        return view('layouts.login');
    }

    public function getRegister() {
        return view('layouts.register');
    }

    public function postLogin() {
        
    }

    public function postRegister() {
        
    }

}
