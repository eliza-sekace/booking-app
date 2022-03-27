<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Exceptions\FormValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Redirect;
use App\Repositories\Listing\PdoListingRepository;
use App\Repositories\Listing\PDOListingsRepository;
use App\Repositories\ProfilesRepository;
use App\Services\Listings\Edit\EditListingRequest;
use App\Services\Listings\Edit\EditListingService;
use App\Services\Listings\Show\ShowListingRequest;
use App\Services\Listings\Show\ShowListingService;
use App\Services\Listings\Store\StoreListingRequest;
use App\Services\Listings\Store\StoreListingService;
use App\Services\Ratings\Show\ArticleRatingService;
use App\Validation\Errors;
use App\Validation\FormValidator;
use App\Views\View;
use Carbon\Carbon;

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
        $listings = new PdoListingRepository();
        return new View("Listings/index.html", [
            'listings' => $listings->index()
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

        } catch (ResourceNotFoundException $e) {
            echo ($e->getMessage()) . "<br>";
            return new View('404.html');
        }
        $rating = new ArticleRatingService();

        return new View("Listings/show.html", [
            'listing' => $response->getListing(),
            'profile' => $profile,
            'currentUser' => $_SESSION['user_id'],
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
        $listings = new PdoListingRepository();
        $service = new StoreListingService($listings);

        $service->execute(new StoreListingRequest(
            $_SESSION['user_id'],
            $_POST['name'],
            $_POST['address'],
            $_POST['description'],
            $_POST['price'],
            $_POST['available_from'],
            $_POST['available_till'] ?? '',
            $_POST['img_path']
        ));
        return new Redirect('/listings');
    }

    public function delete(array $vars)
    {
        $apartmentId = (int)$vars['id'];
        $result = new PdoListingRepository();

        if ($_SESSION['user_id'] == $result->getById($apartmentId)->getUserId()) {
            Connection::connect()
                ->delete('apartments', ['id' => (int)$vars['id']]);
        }
        return new Redirect('/listings');
    }

    public function edit($vars)
    {
        $apartmentId = (int)$vars['id'];
        $service = new EditListingService();
        $response = $service->execute(new EditListingRequest($apartmentId));

        if (($_SESSION['user_id']) == $response->getUserId()) {
            return new View("Listings/edit.html", [
                "listing" => $response,
                'minDate' => Carbon::now()->format('Y-m-d')
            ]);
        } else return new Redirect('/listings');
    }

    public function update(array $vars)
    {
        $apartmentId = (int)$vars['id'];
        $listing = new PdoListingRepository();

        if ($_SESSION['user_id'] == $listing->getById($apartmentId)->getUserId()) {
            Connection::connect()
                ->update('apartments', [
                    'name' => $_POST['name'],
                    'address' => $_POST['address'],
                    'description' => $_POST['description']
                ],
                    ['id' => $apartmentId]);
        }
        return new Redirect("/listings/{$apartmentId}");
    }
}