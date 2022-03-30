<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Redirect;
use App\Services\Listings\Show\ShowListingService;
use App\Services\Ratings\Show\ListingRatingService;
use Psr\Container\ContainerInterface;

class ReviewsController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        if (!isset($_SESSION['user_id'])) {
            header("location: /login", true);
        }
        $this->container = $container;
    }

    public function create($vars)
    {
        $result =  $this->container->get(ShowListingService::class);
        $apartmentId = $vars['id'];
        $userId = $_SESSION['user_id'];

        $review =$this->container->get(ListingRatingService::class);
        $review = $review->checkIfLeftReview($apartmentId, $userId);

        if (!$review) {
            Connection::connect()
                ->insert('reviews', [
                    'user_id' => $_SESSION['user_id'],
                    'apartment_id' => $result->getById($apartmentId)->getId(),
                    'review' => $_POST['review'],
                    'rating' => $_POST['rating']
                ]);
        }
        return new Redirect("/listings/{$vars["id"]}");
    }



}

