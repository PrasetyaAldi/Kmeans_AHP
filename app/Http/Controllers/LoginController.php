<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Services\LoginService;

class LoginController extends Controller
{
    /**
     * Display page Login
     */
    public function index()
    {
        return view('layouts.login');
    }

    /**
     * post login
     */
    public function login(Request $request, LoginService $loginService)
    {
        $data = $request->except('_token');
        try {
            $user = $loginService->login($data);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        Auth::login($user);
        return redirect()->intended(route('home.index'));
    }

    /**
     * logout
     * 
     */
    public function logout()
    {

        Auth::logout();

        return to_route('login');
    }
}
