<?php

namespace App\Services\Listings\Show;

use App\Database\Connection;
use App\Repositories\Listing\ListingRepository;
use App\Repositories\Listing\PdoListingRepository;
use App\Repositories\ProfilesRepository;
use App\Repositories\Reviews\PdoReviewRepository;
use App\Repositories\Reviews\ReviewRepository;
use App\Services\Ratings\Show\ArticleRatingService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ShowListingService
{
    private ListingRepository $listingRepository;
    private ReviewRepository $reviewRepository;

    public function __construct()
    {
        $this->listingRepository = new PdoListingRepository();
        $this->reviewRepository = new PdoReviewRepository();
    }

    public function execute(ShowListingRequest $request): ShowListingResponse
    {
        $result = $this->listingRepository;
        $apartmentId = $result->getById($request->getApartmentId());
        $profileRepository = new ProfilesRepository();
//        $profile = $profileRepository->getByUserId($apartmentId->getUserId());

        $connection = Connection::connect();

        $reviews = $this->reviewRepository->getReviews($apartmentId->getId());
        $reviewCount = $this->reviewRepository->getReviewCount($apartmentId->getId());
        $reviewSum = $this->reviewRepository->getReviewSum($apartmentId->getId());

        $rating = new ArticleRatingService();

        //get start available dates
        $availableDates = $connection
            ->createQueryBuilder()
            ->select('id', 'available_from', 'available_till')
            ->from('apartments')
            ->where('id=?')
            ->setParameter(0, $request->getApartmentId())
            ->executeQuery()
            ->fetchAssociative();

        //get all booked dates
        $allReservations = $connection
            ->createQueryBuilder()
            ->select('reserve_from', 'reserve_till')
            ->from('reservations')
            ->where('apartment_id=?')
            ->setParameter(0, $request->getApartmentId())
            ->executeQuery()
            ->fetchAllAssociative();

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