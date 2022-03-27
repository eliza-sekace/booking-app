<?php
namespace App\Services\Listings\Show;

use App\Database\Connection;
use App\Models\Listing;
use App\Repositories\Listing\ListingRepository;
use App\Repositories\Listing\PDOListingsRepository;
use App\Repositories\ProfilesRepository;
use App\Services\Ratings\Show\ArticleRatingService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ShowListingService
{
    private ListingRepository $listingRepository;

    public function __construct()
    {
    }

    public function execute(ShowListingRequest $request): ShowListingResponse
    {
        $result=new PDOListingsRepository;
        $listing=$result->getById($request->getApartmentId());
        $apartment = new Listing(
            $listing->getId(),
            $listing->getUserId(),
            $listing->getName(),
            $listing->getAddress(),
            $listing->getDescription(),
            $listing->getAvailableFrom(),
            $listing->getAvailableTill(),
            $listing->getImgPath(),
            $listing->getPrice());

        $profileRepository = new ProfilesRepository();
//        $profile = $profileRepository->getByUserId($listing->getUserId());
////        $currentUser = $_SESSION['user_id'];

$connection=Connection::connect();
        $reviews = $connection
            ->createQueryBuilder()
            ->select('r.id', 'r.user_id', 'r.apartment_id', 'r.review', 'r.rating', 'r.created_at',
                'p.name', 'p.surname')
            ->from('reviews', 'r')
            ->leftJoin('r', 'user_profiles', 'p', 'r.user_id=p.user_id')
            ->where('r.apartment_id = ?')
            ->setParameter(0, $request->getApartmentId())
            ->orderBy('r.id', 'desc')
            ->executeQuery()
            ->fetchAllAssociative();

        $reviewCount = $connection
            ->createQueryBuilder()
            ->select('COUNT("rating")')
            ->from("reviews")
            ->where('apartment_id =?')
            ->setParameter(0, $request->getApartmentId())
            ->executeQuery()
            ->fetchAssociative();

        $reviewSum = $connection
            ->createQueryBuilder()
            ->select('rating')
            ->from("reviews")
            ->where('apartment_id =?')
            ->setParameter(0, $request->getApartmentId())
            ->executeQuery()
            ->fetchAllAssociative();

        $rating=new ArticleRatingService();

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
        if (Carbon::now()->lte($availableDates['available_from'])){
            $minDate=$availableDates['available_from'];
        } else $minDate=Carbon::now()->format('Y-m-d');
        return new ShowListingResponse(
            $listing,
            $reviews,
            $minDate,
            $availableDates,
            $reservedDates,
            $reviewCount,
            $reviewSum
        );
    }


}