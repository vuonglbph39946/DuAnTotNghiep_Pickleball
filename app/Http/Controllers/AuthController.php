<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {

        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6'
        ]);

        User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password),
            'role'=>'customer',
            'status'=>1
        ]);

        return redirect('/login')->with('success','Đăng ký thành công');
    }


    public function login(Request $request)
    {

        $credentials = $request->only('email','password');

        if(Auth::attempt($credentials))
        {
            return redirect('/');
        }

        return back()->with('error','Sai email hoặc mật khẩu');
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

}