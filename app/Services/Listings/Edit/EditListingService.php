<?php
namespace App\Services\Listings\Edit;

use App\Database\Connection;
use App\Models\Listing;
use App\Repositories\Listing\ListingRepository;

class EditListingService
{
    private ListingRepository $listingRepository;

    public function __construct()
    {
    }

    public function execute(EditListingRequest $request): Listing
    {
        $connection = Connection::connect();
        $result = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'address', 'description', 'available_from', 'available_till', 'img_path', 'price')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $request->getApartmentId())
            ->executeQuery()
            ->fetchAssociative();

        $apartment = Listing::make($result);

        return $apartment;

    }

}

