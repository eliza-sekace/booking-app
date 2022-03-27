<?php

namespace App\Controllers;

use App\Redirect;
use App\Repositories\UsersRepository;
use App\Views\View;

class LoginController
{
    private UsersRepository $repository;

    public function __construct()
    {
        $this->repository = new UsersRepository();
    }

    public function signin()
    {
        if (isset($_SESSION['user_id'])) {
            header("location: /listings", true);
        }

        return new View("Auth/login.html");
    }

    public function login()
    {
        $user = $this->repository->getByEmail($_POST['email']);
        if (is_null($user)) {
            header("location: /login", true);
        }
        if (!$user->checkPassword($_POST['password'])) {
            header("location: /login", true);
        }
        session_start();
        $_SESSION['user_id'] = $user->getId();
        return new Redirect('/listings');
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("location:/login", true);
    }
}