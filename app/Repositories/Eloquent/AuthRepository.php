<?php


namespace App\Repositories\Eloquent;


use App\Models\User;

use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use function Nette\Utils\first;

class AuthRepository implements AuthRepositoryInterface
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function findByEmail(string $email)
    {
        return User::where('email',$email)->first();
    }

    public function logout()
    {
        Auth::logout();
    }
}
