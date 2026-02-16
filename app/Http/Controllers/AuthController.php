<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Repositories\Eloquent\AuthRepository;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index()
    {
        return view('auth.login');
    }
    public function authLogin(AuthLoginRequest $request)
    {
        $email = $request->email;
        $password = $request->password;

        $loginResult = $this->authService->AuthLogin($email, $password);

        if ($loginResult['status']) {
            return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
        } else {
            return back()->with('error', $loginResult['message']);
        }
    }
    public function dashboard()
    {
        return view('product.index');
    }
}
