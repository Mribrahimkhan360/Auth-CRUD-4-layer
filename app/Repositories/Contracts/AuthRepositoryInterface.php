<?php


namespace App\Repositories\Contracts;


interface AuthRepositoryInterface
{
    public function create(array $data);
    public function findByEmail(string $email);
}
