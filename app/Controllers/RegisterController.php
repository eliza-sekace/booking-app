<?php

namespace App\Controllers;

use App\Exceptions\FormValidationException;
use App\Redirect;
use App\Repositories\UsersRepository;
use App\Validation\FormValidator;
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
        try {
            $validator = new FormValidator($_POST, [
                'password' => ['required', "min:6"],
                'password_confirmation' => ['required', "min:6"],
                'email' => ['required']

            ]);
            $validator->passes();
        } catch (FormValidationException $exception) {

            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new View('Auth/register.html',[
                'errors'=>$_SESSION['errors']]);
        }
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            header("location: /register", true);
        }
        $user = $this->repository->store($_POST);

        if (is_null($user)) {
            header("location: /register", true);
        }

        if (!session_start()){
            session_start();
        }
        $_SESSION['user_id'] = $user->getId();

        return new Redirect("/listings");
    }
}