<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(){
        $user = Auth::user();
        $genders = config('statuses.genders');
        return view('profiles.index',compact('user','genders'));
    }
}
