<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Article;
use App\Models\Listing;
use App\Redirect;
use App\Repositories\ProfilesRepository;
use App\Repositories\ReservationsRepository;
use App\Views\View;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class ListingsController
{
    public function index()
    {
        $connection = Connection::connect();
        $apartments = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'address', 'description', 'available_from', 'available_till',
                'img_path', 'price')
            ->from('apartments')
            ->executeQuery()
            ->fetchAllAssociative();

        $listings = [];
        foreach ($apartments as $apartment) {
            $listings[] = new Listing(
                $apartment['id'],
                $apartment['user_id'],
                $apartment['name'],
                $apartment['address'],
                $apartment['description'],
                $apartment['available_from'],
                $apartment['available_till'],
                $apartment['img_path'],
                $apartment['price']);
        }
        return new View("Listings/index.html", [
            'listings' => $apartments
        ]);
    }

    public function show($vars)
    {
        $connection = Connection::connect();
        $result = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'address', 'description', 'available_from',
                'available_till', 'img_path', 'price')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $vars["id"])
            ->executeQuery()
            ->fetchAssociative();


        $apartment = new Listing(
            $result['id'],
            $result['user_id'],
            $result['name'],
            $result['address'],
            $result['description'],
            $result['available_from'],
            $result['available_till'],
            $result['img_path'],
            $result['price']);

        $profileRepository = new ProfilesRepository();
        $profile = $profileRepository->getByUserId($result['user_id']);
        $currentUser = $_SESSION['user_id'];


        $reviews = $connection
            ->createQueryBuilder()
            ->select('r.id', 'r.user_id', 'r.apartment_id', 'r.review', 'r.rating', 'r.created_at',
                'p.name', 'p.surname')
            ->from('reviews', 'r')
            ->leftJoin('r', 'user_profiles', 'p', 'r.user_id=p.user_id')
            ->where('r.apartment_id = ?')
            ->setParameter(0, $vars["id"])
            ->orderBy('r.id', 'desc')
            ->executeQuery()
            ->fetchAllAssociative();

        $reviewCount = $connection
            ->createQueryBuilder()
            ->select('COUNT("rating")')
            ->from("reviews")
            ->where('apartment_id =?')
            ->setParameter(0, $vars['id'])
            ->executeQuery()
            ->fetchAssociative();

        $reviewSum = $connection
            ->createQueryBuilder()
            ->select('rating')
            ->from("reviews")
            ->where('apartment_id =?')
            ->setParameter(0, $vars['id'])
            ->executeQuery()
            ->fetchAllAssociative();

        if ($reviewCount['COUNT("rating")'] == false) {
            $averageRating = "No reviews yet!";
            $averageRatingStars = '';
        } else {
            $reviewRatings = 0;
            foreach ($reviewSum as $review) {
                $reviewRatings += $review['rating'];
            }
            $averageRating = round($reviewRatings / (int)$reviewCount['COUNT("rating")'], 1);
            $averageRatingStars = str_repeat("★", round($averageRating, 0));
        }


            //get start available dates
        $availableDates = $connection
            ->createQueryBuilder()
            ->select('id', 'available_from', 'available_till')
            ->from('apartments')
            ->where('id=?')
            ->setParameter(0, $vars['id'])
            ->executeQuery()
            ->fetchAssociative();

        //get all booked dates
        $allReservations = $connection
            ->createQueryBuilder()
            ->select('reserve_from', 'reserve_till')
            ->from('reservations')
            ->where('apartment_id=?')
            ->setParameter(0, $vars['id'])
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


        return new View("Listings/show.html", [
            'listing' => $apartment,
            'profile' => $profile,
            'currentUser' => $currentUser,
            'reviews' => $reviews,
            'minDate' => $minDate,
            'averageRating' => $averageRating,
            'averageRatingStars' => $averageRatingStars,
            'available_from' => $availableDates['available_from'],
            'available_till' => $availableDates['available_till'],
            'reserved_dates'=>$reservedDates
        ]);
    }

    public function create()
    {
        return new View("Listings/create.html", [
            'inputs' => $_SESSION['inputs'] ?? [],
            'minDate' => Carbon::now()->format('Y-m-d')
        ]);
    }

    public function store($vars): Redirect
    {
        $connection = Connection::connect();
        $availableFrom = null;
        if ($_POST['available_from'] == null) {
            $availableFrom = Carbon::now()->format('Y-m-d');
        }
        $result = $connection
            ->insert('apartments', [
                'user_id' => $_SESSION['user_id'],
                'name' => $_POST['name'],
                'address' => $_POST['address'],
                'description' => $_POST['description'],
                'available_from' => $availableFrom,
                'available_till' => $_POST['available_till'],
                'img_path' => $_POST['img_path'],
                'price' => $_POST['price']
            ]);

        return new Redirect('/listings');
    }

    public function delete(array $vars)
    {
        $connection = Connection::connect();

        $result = $connection
            ->createQueryBuilder()
            ->select('user_id')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $vars["id"])
            ->executeQuery()
            ->fetchAssociative();

        if ($_SESSION['user_id'] == $result['user_id']) {
            $connection
                ->delete('apartments', ['id' => (int)$vars['id']]);
        }
        return new Redirect('/listings');
    }

    public function edit($vars): View
    {
        $connection = Connection::connect();

        $result = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'address', 'description', 'available_from', 'available_till', 'img_path', 'price')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $vars["id"])
            ->executeQuery()
            ->fetchAssociative();

        $apartment = new Listing(
            $result['id'],
            $result['user_id'],
            $result['name'],
            $result['address'],
            $result['description'],
            $result['available_from'],
            $result['available_till'],
            $result['img_path'],
            $result['price']);

        return new View("Listings/edit.html", [
            "listing" => $apartment,
            'minDate' => Carbon::now()->format('Y-m-d')
        ]);
    }

    public function update(array $vars)
    {
        $connection = Connection::connect();

        $result = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'address', 'description', 'available_from', 'available_till')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $vars["id"])
            ->executeQuery()
            ->fetchAssociative();

        if ($_SESSION['user_id'] == $result['user_id']) {
            $connection
                ->update('apartments', [
                    'name' => $_POST['name'],
                    'address' => $_POST['address'],
                    'description' => $_POST['description']
                ], ['id' => (int)$vars['id']]);
        }
        header("location: /listings/{$vars['id']}", true);
    }
}