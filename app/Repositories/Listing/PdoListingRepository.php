<?php
namespace App\Repositories\Listing;

use App\Database\Connection;
use App\Models\Listing;

class PdoListingRepository implements ListingRepository
{
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

    public function save(Listing $listing):void
    {
        Connection::connect()
            ->insert('apartments', [
                'user_id' => $listing->getUserId(),
                'name' => $listing->getName(),
                'address' => $listing->getAddress(),
                'description' => $listing->getDescription(),
                'available_from' => $listing->getAvailableFrom(),
                'available_till' => $listing->getAvailableTill(),
                'img_path' => $listing->getImgPath(),
                'price' =>$listing->getPrice()
            ]);
    }

    public function index(): array
    {
        $apartments = Connection::connect()
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'address', 'description', 'available_from', 'available_till',
                'img_path', 'price')
            ->from('apartments')
            ->executeQuery()
            ->fetchAllAssociative();

        $listings = [];
        foreach ($apartments as $apartment) {
            $listings[] = new Listing(
                $apartment['id'],
                $apartment['user_id'],
                $apartment['name'],
                $apartment['address'],
                $apartment['description'],
                $apartment['available_from'],
                $apartment['available_till'],
                $apartment['img_path'],
                $apartment['price']);
        }
        return $listings;
    }

    public function getApartmentData(int $id): array
    {
        $connection = Connection::connect();
        $result = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'address', 'description', 'available_from', 'available_till', 'img_path', 'price')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $id)
            ->executeQuery()
            ->fetchAssociative();

        return $result;
    }


}