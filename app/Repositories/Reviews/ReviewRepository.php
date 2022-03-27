<?php
namespace App\Repositories\Reviews;

interface ReviewRepository
{
    public function getReviews(int $apartmentId):array;
    public function getReviewCount(int $apartmentId):array;
    public function getReviewSum(int $apartmentId):array;
    public function checkIfLeftReview(int $apartmentId, int $userId):bool;

}
