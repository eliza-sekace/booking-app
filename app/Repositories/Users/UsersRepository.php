<?php

namespace App\Repositories\Users;

use App\Database\Connection;
use App\Models\User;

class UsersRepository
{
    private \Doctrine\DBAL\Connection $connection;

    public function __construct()
    {
        $this->connection = Connection::connect();
    }

    public function getById(int $id): ?User
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('id', 'email', 'password')
            ->from('users')
            ->where('id = ?')
            ->setParameter(0, $id)
            ->executeQuery()
            ->fetchAssociative();

        return User::make($result);
    }

    public function getByEmail(string $email): ?User
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('id', 'email', 'password')
            ->from('users')
            ->where('email = ?')
            ->setParameter(0, $email)
            ->executeQuery()
            ->fetchAssociative();

        return User::make($result);
    }

    public function store(array $attributes): ?User
    {
        $email = $_POST['email'];
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $this->connection
            ->insert('users', [
                'email' => $email,
                'password' => $hashedPassword
            ]);

        $user = $this->getByEmail($email);
//        $userId = $this->connection->lastInsertId();

        $this->connection
            ->insert('user_profiles', [
                'user_id' => $user->getId(),
                'name' => $_POST['name'],
                'surname' => $_POST['surname']
            ]);

        return $user;
    }
}