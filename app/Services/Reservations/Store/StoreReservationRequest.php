<?php
namespace App\Services\Reservations\Store;

class StoreReservationRequest
{
    private int $apartmentId;
    public function __construct(int $apartmentId)
    {
        $this->apartmentId = $apartmentId;

    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }

}
