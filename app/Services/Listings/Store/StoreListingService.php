<?php
namespace App\Services\Listings\Store;

use App\Database\Connection;

class StoreListingService
{
    public function execute(StoreListingRequest $request)
    {
      Connection::connect()
            ->insert('apartments', [
                'user_id' => $request->getUserId(),
                'name' => $request->getName(),
                'address' => $request->getAddress(),
                'description' => $request->getDescription(),
                'available_from' => $request->getAvailableFrom(),
                'available_till' => $request->getAvailableTill(),
                'img_path' => $request->getImgPath(),
                'price' =>$request->getPrice()
            ]);

    }
}