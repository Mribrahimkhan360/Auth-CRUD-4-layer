<?php


namespace App\Repositories;


class AuthRepositoryInterface
{
    public function create(array $data);
    public function findByEmail(string $email);
}
