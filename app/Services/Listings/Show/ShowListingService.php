<?php

namespace App\Services\Listings\Show;

use App\Repositories\Listing\ListingRepository;
use App\Repositories\Reservations\ReservationRepository;
use App\Repositories\Reviews\ReviewRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ShowListingService
{
    private ListingRepository $listingRepository;
    private ReviewRepository $reviewRepository;
    private ReservationRepository $reservationRepository;

    public function __construct(ListingRepository $listingRepository, ReviewRepository $reviewRepository, ReservationRepository $reservationRepository)
    {
        $this->listingRepository = $listingRepository;
        $this->reviewRepository = $reviewRepository;
        $this->reservationRepository = $reservationRepository;
    }

    public function execute(ShowListingRequest $request): ShowListingResponse
    {
        $result = $this->listingRepository;
        $apartmentId = $result->getById($request->getApartmentId());

        $reviews = $this->reviewRepository->getReviews($apartmentId->getId());
        $reviewCount = $this->reviewRepository->getReviewCount($apartmentId->getId());
        $reviewSum = $this->reviewRepository->getReviewSum($apartmentId->getId());

        $availableDates = $this->reservationRepository->getAvailableDates($apartmentId->getId());
        $allReservations = $this->reservationRepository->getAllReservations($apartmentId->getId());

        $reservedDates = [];
        foreach ($allReservations as $reservation) {
            $period = CarbonPeriod::create($reservation['reserve_from'], $reservation['reserve_till']);
            foreach ($period as $date) {
                $reservedDates[] = $date->format('Y-m-d');
            }
        }

        if (Carbon::now()->lte($availableDates['available_from'])) {
            $minDate = $availableDates['available_from'];
        } else $minDate = Carbon::now()->format('Y-m-d');

        return new ShowListingResponse(
            $apartmentId,
            $reviews,
            $minDate,
            $availableDates,
            $reservedDates,
            $reviewCount,
            $reviewSum
        );
    }
    public function index()
    {
        return $this->listingRepository->index();
    }

    public function getById($id)
    {
        return $this->listingRepository->getById($id);
    }

}