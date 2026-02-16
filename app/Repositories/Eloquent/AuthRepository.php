<?php


namespace App\Repositories\Eloquent;


use App\Models\User;
use App\Repositories\AuthRepositoryInterface;
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
}
