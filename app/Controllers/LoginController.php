<?php

namespace App\Controllers;

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
        // Find user with email
        $user = $this->repository->getByEmail($_POST['email']);

        // If no user redirect to login
        if (is_null($user)) {
            header("location: /login", true);
        }

        // Check password
        if (!$user->checkPassword($_POST['password'])) {
            header("location: /login", true);
            return;
        }

        // If ok, start session, add user id to session, redirect to articles
        session_start();
        $_SESSION['user_id'] = $user->getId();

        header("location: /listings", true);
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("location:/login", true);
    }
}