<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(){
        $data = $this->profile();
        return view('profiles.index',compact('data'));
    }

    private function profile(){
        $user = Auth::user()->with('role')->first();
        $genders = config('statuses.genders');

        return compact('user','genders');
    }
}
