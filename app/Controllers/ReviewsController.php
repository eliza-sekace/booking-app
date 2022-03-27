<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Redirect;
use App\Repositories\Listing\PdoListingRepository;
use App\Repositories\Reviews\PdoReviewRepository;

class ReviewsController
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("location: /login", true);
        }
    }

    public function create($vars)
    {
        $result = new PdoListingRepository();
        $apartmentId = $vars['id'];
        $userId = $_SESSION['user_id'];

        $review = new PdoReviewRepository();
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

