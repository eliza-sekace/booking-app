<?php
namespace App\Services\Listings\Edit;

use App\Models\Listing;
use App\Repositories\Listing\PdoListingRepository;

class EditListingService
{
    public function execute(EditListingRequest $request): Listing
    {
        $listing = new PdoListingRepository();
        $listing->getById($request->getApartmentId());
        $apartment = Listing::make($listing->getApartmentData($request->getApartmentId()));
        return $apartment;
    }

}

