<?php

namespace App\Repositories;

use App\Database\Connection;
use App\Models\Profile;
use App\Models\User;

class ReservationsRepository
{
    private \Doctrine\DBAL\Connection $connection;

    public function __construct()
    {
        $this->connection = Connection::connect();
    }

    public function getAvailable($vars)
    {
        $availableDates = $this->connection
            ->createQueryBuilder()
            ->select('id','available_from', 'available_till')
            ->from('apartments')
            ->where('id=?')
            ->setParameter(0, $vars)
            ->executeQuery()
            ->fetchAssociative();

        return $availableDates;
    }
}