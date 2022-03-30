<?php
namespace App\Services\Reservations\Store;

use App\Repositories\Reservations\PdoReservationRepository;
use App\Repositories\Reservations\ReservationRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StoreReservationService
{
    private ReservationRepository $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function execute(StoreReservationRequest $request, $reserveFrom, $reserveTill):bool
    {
        $reservations = $this->reservationRepository->getAllReservations($request->getApartmentId());
        $startDate = Carbon::parse($reserveFrom)->toDateString();
        $endDate = Carbon::parse($reserveTill)->toDateString();
        $overlapCheck = false;

        $period = CarbonPeriod::between($startDate, $endDate);
        foreach ($reservations as $reservation) {
            $check = $period->overlaps(
                Carbon::parse($reservation['reserve_from']),
                Carbon::parse($reservation['reserve_till'])
            );
            if ($check == true) {
                $overlapCheck = true;
            }
        }

        $betweenAvailable = false;
        $availableDates = $this->reservationRepository->getAvailableDates($request->getApartmentId());
         if (Carbon::parse($availableDates['available_from'])->lte(Carbon::parse($reserveFrom)) &&
                Carbon::parse($availableDates['available_till'])->gte(Carbon::parse($reserveTill))){
                $betweenAvailable = true;
            }

        if (Carbon::parse($reserveTill)->gt(Carbon::parse($reserveFrom))
            && $overlapCheck == false && $betweenAvailable == true) {
            return true;
        }
        else return false;
    }

    public function store($userId,$apartmentId, $reserveFrom, $reserveTill)
    {
     $this->reservationRepository->store($userId, $apartmentId, $reserveFrom, $reserveTill);
    }
}