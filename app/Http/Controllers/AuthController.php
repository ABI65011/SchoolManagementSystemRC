<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->only('email', 'password'), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Validation errors')
                ->withErrors($validator->errors())->onlyInput('email');
        }

        $validated = $validator->validated();

        if (Auth::attempt($validated, true)) {
            $request->session()->regenerate();
            return  redirect()->intended();
        }

        return back()->with('error', 'Invalid credentials')->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
