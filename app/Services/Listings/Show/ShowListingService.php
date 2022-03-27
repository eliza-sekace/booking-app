<?php

namespace App\Services\Listings\Show;

use App\Repositories\Listing\ListingRepository;
use App\Repositories\Listing\PdoListingRepository;
use App\Repositories\Reservations\ReservationRepository;
use App\Repositories\Reservations\PdoReservationRepository;
use App\Repositories\Reviews\PdoReviewRepository;
use App\Repositories\Reviews\ReviewRepository;
use App\Services\Ratings\Show\ArticleRatingService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ShowListingService
{
    private ListingRepository $listingRepository;
    private ReviewRepository $reviewRepository;
    private ReservationRepository $reservationRepository;

    public function __construct()
    {
        $this->listingRepository = new PdoListingRepository();
        $this->reviewRepository = new PdoReviewRepository();
        $this->reservationRepository = new PdoReservationRepository();
    }

    public function execute(ShowListingRequest $request): ShowListingResponse
    {
        $result = $this->listingRepository;
        $apartmentId = $result->getById($request->getApartmentId());

        $reviews = $this->reviewRepository->getReviews($apartmentId->getId());
        $reviewCount = $this->reviewRepository->getReviewCount($apartmentId->getId());
        $reviewSum = $this->reviewRepository->getReviewSum($apartmentId->getId());

        $rating = new ArticleRatingService();
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


}