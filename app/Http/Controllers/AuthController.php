<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 로그인 폼
    public function showLogin()
    {
        return view('auth.login');
    }

    // 로그인 처리
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'username' => '아이디 또는 비밀번호가 일치하지 않습니다.',
        ])->onlyInput('username');
    }

    // 로그아웃
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    // 회원가입 폼
    public function showRegister()
    {
        return view('auth.register');
    }

    // 회원가입 처리
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users|alpha_dash|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'name' => 'required|max:100',
        ], [
            'username.required' => '아이디를 입력하세요.',
            'username.unique' => '이미 사용중인 아이디입니다.',
            'email.required' => '이메일을 입력하세요.',
            'email.unique' => '이미 사용중인 이메일입니다.',
            'password.required' => '비밀번호를 입력하세요.',
            'password.min' => '비밀번호는 최소 6자 이상이어야 합니다.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'name.required' => '이름을 입력하세요.',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'name' => $validated['name'],
        ]);

        $user->sendEmailVerificationNotification();

        Auth::login($user);

        return redirect('/email/verify')->with('success', '회원가입이 완료되었습니다! 인증 메일을 확인해주세요.');
    }
}