<?php
namespace App\Repositories\Reviews;

use App\Models\Listing;

interface ReviewRepository
{
    public function getReviews(int $apartmentId):array;
    public function getReviewCount(int $apartmentId):array;
    public function getReviewSum(int $apartmentId):array;

}
