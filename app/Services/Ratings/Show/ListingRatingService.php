<?php

namespace App\Services\Ratings\Show;

use App\Repositories\Reviews\ReviewRepository;

class ListingRatingService
{
    private ReviewRepository $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    private string $averageRating = "No reviews yet";
    private string $averageRatingStars = '';

    public function getStarRating($reviewCount, $reviewSum)
    {
        $rating = [];
        if ($reviewCount['COUNT("rating")'] == false) {
            $rating = [
                'averageRating' => $this->averageRating,
                'averageRatingStars' => $this->averageRatingStars
            ];
            $this->averageRating = "No reviews yet!";
            $this->averageRatingStars = '';
        } else {
            $reviewRatings = 0;
            foreach ($reviewSum as $review) {
                $reviewRatings += $review['rating'];
            }
            $this->averageRating = round($reviewRatings / (int)$reviewCount['COUNT("rating")'], 1);
            $this->averageRatingStars = str_repeat("★", round($this->averageRating, 0));
            $rating = [
                'averageRating' => $this->averageRating,
                'averageRatingStars' => $this->averageRatingStars
            ];
        }
        return $rating;
    }

    public function checkIfLeftReview($apartmentId, $userId)
    {
        return $this->reviewRepository->checkIfLeftReview($apartmentId, $userId);
    }


}
