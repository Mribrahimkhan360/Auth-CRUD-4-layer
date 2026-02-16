<?php


namespace App\Repositories\Eloquent;


use App\Models\User;

use App\Repositories\Contracts\AuthRepositoryInterface;
use function Nette\Utils\first;

class AuthRepository implements AuthRepositoryInterface
{
    public function create(array $data)
    {
        return User::created($data);
    }

    public function findByEmail(string $email)
    {
        return User::where('email',$email)->first();
    }
}
