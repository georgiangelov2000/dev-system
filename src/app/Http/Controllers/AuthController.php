<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Response;
use App\Traits\ThrottlesAttempts;
use Exception;

class AuthController extends Controller {

    use ThrottlesAttempts;

    public function home() {
        return view('app');
    }

    public function postLogin(Request $request) {

        if ($this->hasTooManyAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $request->validate([
            'email' => 'required|exists:users|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($credentials, $request['remember'])) {
            $request->session()->regenerate();
            
            $this->clearAttempts($request);
            
            return redirect()->intended('home');
        }
            
        $this->incrementAttempts($request);

        return back()->with('danger', 'Wrong password please try again!');
    }

    public function postRegister(UserRequest $request) {
        $credentials = $request->validated();

        $credentials = (object) $credentials;

        DB::beginTransaction();

        try {

            User::create([
                'email' => $credentials->email,
                'username' => $credentials->username,
                'password' => Hash::make($credentials->password),
            ]);

            DB::commit();

            Log::info('Succesfully registered user');
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            Log::info($e->getMessage());
        }

        return back();
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

}
