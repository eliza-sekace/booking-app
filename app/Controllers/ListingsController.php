<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Exceptions\FormValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Redirect;
use App\Repositories\Users\ProfilesRepository;
use App\Services\Listings\Edit\EditListingRequest;
use App\Services\Listings\Edit\EditListingService;
use App\Services\Listings\Show\ShowListingRequest;
use App\Services\Listings\Show\ShowListingService;
use App\Services\Listings\Store\StoreListingRequest;
use App\Services\Listings\Store\StoreListingService;
use App\Services\Ratings\Show\ListingRatingService;
use App\Validation\Errors;
use App\Validation\FormValidator;
use App\Views\View;
use Carbon\Carbon;
use Psr\Container\ContainerInterface;

class ListingsController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        if (!isset($_SESSION['user_id'])) {
            header("location: /login", true);
        }
        $this->container = $container;
    }

    public function index()
    {
        $service = $this->container->get(ShowListingService::class);
        return new View("Listings/index.html", [
            'listings' => $service->index()
        ]);
    }

    public function show($vars)
    {
        try {
            $apartmentId = (int)$vars['id'];
            $service =  $this->container->get(ShowListingService::class);
            $response = $service->execute(new ShowListingRequest($apartmentId));
//            $result = new PdoListingRepository();
            if (!$service) {
                throw new ResourceNotFoundException("Article with id {$apartmentId} not found");
            }

            $profileRepository = new ProfilesRepository();
            $profile = $profileRepository->getByUserId($response->getListing()->getUserId());

        } catch (ResourceNotFoundException $e) {
            echo ($e->getMessage()) . "<br>";
            return new View('404.html');
        }
        $rating = $this->container->get(ListingRatingService::class);
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
        $service =  $this->container->get(StoreListingService::class);

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
        $result =  $this->container->get(ShowListingService::class);

        if ($_SESSION['user_id'] == $result->getById($apartmentId)->getUserId()) {
           $result->remove($apartmentId);
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
        $service =  $this->container->get(ShowListingService::class);

        if ($_SESSION['user_id'] == $service->getById($apartmentId)->getUserId()) {
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