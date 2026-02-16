<?php


namespace App\Services;


use App\Repositories\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $authRepository;
    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;

    }


    public function AuthLogin(string $email, string $password, bool $remember = false) // loginUser
    {
        $user = $this->authRepository->findByEmail($email);

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember); // This actually logs in the user
            return ['status' => true, 'message' => 'Login successful'];
        }

        return ['status' => false, 'message' => 'Invalid credentials'];
    }
}
