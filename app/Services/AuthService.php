<?php


namespace App\Services;


use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $authRepository;
    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;

    }


    public function AuthLogin(string $email, string $password)
    {
        $user = $this->authRepository->findByEmail($email);

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user); // login for using
            return ['status' => true, 'message' => 'Login successful'];
        }

        return ['status' => false, 'message' => 'Invalid credentials'];

    }

}
