<?php

namespace App\Controllers;

use App\Repositories\UsersRepository;
use App\Views\View;

class RegisterController
{
    private UsersRepository $repository;

    public function __construct()
    {
        if (isset($_SESSION['user_id'])) {
            header("location: /listings", true);
        }

        $this->repository = new UsersRepository();
    }

    public function signup()
    {
        return new View("Auth/register.html");
    }

    public function register()
    {
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            header("location: /register", true);
        }

        // Store user with email
        $user = $this->repository->store($_POST);

        // If no user redirect to register
        if (is_null($user)) {
            header("location: /register", true);
        }

        // If ok, start session, add user id to session, redirect to articles
        if (!session_start()){
            session_start();
        }
        $_SESSION['user_id'] = $user->getId();

        header("location: /listings", true);
    }
}