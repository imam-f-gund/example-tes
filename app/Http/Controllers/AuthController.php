<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (auth()->attempt($request->only('username', 'password'))) {
            if (auth()->user()->name == 'admin') {
                return redirect()->route('admin')->with('success', 'Anda berhasil login');
            }else if (auth()->user()->name == 'operator') {
                return redirect()->route('operator')->with('success', 'Anda berhasil login');
            }else{
                return redirect()->route('cust')->with('success', 'Anda berhasil login');
            }
            
        }

        return redirect()->route('login')
        ->with('error', 'Username atau password salah');
    }

    public function logout()
    {
        auth()->logout();

        
        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'email' => 'required|unique:users,email',
        ]);
        
        Customer::create([
            'name' => $request->username,
            'email' => $request->email,
        ]);

        User::create([
            'name' => $request->username,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'id_cust'=> Customer::latest()->first()->id,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil');
    }

}
