<?php

namespace App\Models;

class User
{
    private int $id;
    private string $email;
    private string $password;

    public function __construct($id, $email, $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function checkPassword(string $password): bool
    {
        return password_verify($password,$this->password);
    }

    public static function make($attributes)
    {
        if (!isset($attributes['id']) || !isset($attributes['email']) || !isset($attributes['password'])) {
            return null;
        }

        return new self($attributes['id'], $attributes['email'], $attributes['password']);
    }
}


