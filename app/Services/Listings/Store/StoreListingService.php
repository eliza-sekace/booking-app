<?php

namespace App\Services\Listings\Store;

use App\Database\Connection;
use App\Models\Listing;
use App\Repositories\Listing\ListingRepository;
use App\Repositories\Listing\PdoListingRepository;

class StoreListingService
{
    private ListingRepository $listingRepository;

    public function __construct(ListingRepository $listingRepository)
    {
        $this->listingRepository = new PdoListingRepository();
    }

    public function execute(StoreListingRequest $request):Listing
    {
        $id=null;
        $listing = new Listing(
           $id,
            $request->getUserId(),
            $request->getName(),
            $request->getAddress(),
            $request->getDescription(),
            $request->getAvailableFrom(),
            $request->getAvailableTill(),
            $request->getImgPath(),
            $request->getPrice()
        );
        $this->listingRepository->save($listing);
        return $listing;
    }
}






