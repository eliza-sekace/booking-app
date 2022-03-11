<?php


namespace App\Database;

use Doctrine\DBAL\DriverManager;

class Connection
{
    protected static array $params = [
        'dbname' => 'booking_app',
        'user' => 'root',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
    ];

    public static function connect()
    {
        return DriverManager::getConnection(self::$params);
    }
}