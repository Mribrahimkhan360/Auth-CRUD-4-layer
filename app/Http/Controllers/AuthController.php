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
        $credentials = $request->only('email', 'password');

        $result = $this->authService->AuthLogin($credentials['email'], $credentials['password']);

        if ($result['status']) {
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => $result['message']]);
    }
    public function dashboard()
    {
        return view('product.index');
    }
}
