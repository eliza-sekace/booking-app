<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Exceptions\FormValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Listing;
use App\Redirect;
use App\Repositories\Listing\PDOListingsRepository;
use App\Repositories\ProfilesRepository;
use App\Services\Listings\Show\ShowListingRequest;
use App\Services\Listings\Show\ShowListingService;
use App\Services\Listings\Store\StoreListingRequest;
use App\Services\Listings\Store\StoreListingService;
use App\Services\Ratings\Show\ArticleRatingService;
use App\Validation\Errors;
use App\Validation\FormValidator;
use App\Views\View;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class ListingsController
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("location: /login", true);
        }
    }

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
        try {
            $apartmentId = (int)$vars['id'];
            $service = new ShowListingService();
            $response = $service->execute(new ShowListingRequest($apartmentId));

            $result = new PDOListingsRepository;
            if (!$result) {
                throw new ResourceNotFoundException("Article with id {$apartmentId} not found");
            }

            $profileRepository = new ProfilesRepository();
            $profile = $profileRepository->getByUserId($response->getListing()->getUserId());
            $currentUser = $_SESSION['user_id'];

        } catch (ResourceNotFoundException $e) {
            echo ($e->getMessage()) . "<br>";
            return new View('404.html');
        }
        $rating = new ArticleRatingService();

        return new View("Listings/show.html", [
            'listing' => $response->getListing(),
            'profile' => $profile,
            'currentUser' => $currentUser,
            'reviews' => $response->getReviews(),
            'minDate' => $response->getMinDate(),
            'averageRating' => $rating->getStarRating($response->getReviewCount(), $response->getReviewSum())['averageRating'],
            'averageRatingStars' => $rating->getStarRating($response->getReviewCount(), $response->getReviewSum())['averageRatingStars'],
            'available_from' => $response->getAvailableDates()['available_from'],
            'available_till' => $response->getAvailableDates()['available_till'],
            'reserved_dates' => $response->getReservedDates()
        ]);
    }

    public function create()
    {
        return new View("Listings/create.html", [
            'inputs' => $_SESSION['inputs'] ?? [],
            'minDate' => Carbon::now()->format('Y-m-d'),
            'errors' => Errors::getAll()
        ]);
    }

    public function store(): Redirect
    {
        try {
            $validator = new FormValidator($_POST, [
                'name' => ['required', "min:3"],
                'address' => ['required'],
                'description' => ['required'],
                'price' => ['required'],

            ]);
            $validator->passes();
        } catch (FormValidationException $exception) {

            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
        }

        $connection = Connection::connect();
        $availableFrom = null;
        if ($_POST['available_from'] == null) {
            $availableFrom = Carbon::now()->format('Y-m-d');
        }

        $service = new StoreListingService();
        $service->execute(new StoreListingRequest(
            $_SESSION['user_id'],
            $_POST['name'],
            $_POST['address'],
            $_POST['description'],
            $availableFrom,
            $_POST['available_till'],
            $_POST['img_path'],
            $_POST['price']
        ));


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

    public function edit($vars)
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


        if (($_SESSION['user_id']) == $result['user_id']) {
            return new View("Listings/edit.html", [
                "listing" => $apartment,
                'minDate' => Carbon::now()->format('Y-m-d')
            ]);
        } else return new Redirect('/listings');

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