<?php


namespace App\Repositories\Listing;

use App\Database\Connection;
use App\Models\Listing;

class PDOListingsRepository
{
    private \Doctrine\DBAL\Connection $connection;

    public function __construct()
    {
        $this->connection = Connection::connect();
    }

    public function getById(int $id): ?Listing
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'address', 'description', 'available_from',
                'available_till', 'img_path', 'price')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $id)
            ->executeQuery()
            ->fetchAssociative();

        return Listing::make($result);
    }


}
