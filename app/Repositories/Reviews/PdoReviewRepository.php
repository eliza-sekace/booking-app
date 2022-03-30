<?php
namespace App\Repositories\Reviews;

use App\Database\Connection;

class PdoReviewRepository implements ReviewRepository
{
    public function getReviews(int $apartmentId): array
    {
        $connection = Connection::connect();
        $reviews = $connection
            ->createQueryBuilder()
            ->select('r.id', 'r.user_id', 'r.apartment_id', 'r.review', 'r.rating', 'r.created_at',
                'p.name', 'p.surname')
            ->from('reviews', 'r')
            ->leftJoin('r', 'user_profiles', 'p', 'r.user_id=p.user_id')
            ->where('r.apartment_id = ?')
            ->setParameter(0, $apartmentId)
            ->orderBy('r.id', 'desc')
            ->executeQuery()
            ->fetchAllAssociative();

        return $reviews;
    }

    public function getReviewCount(int $apartmentId): array
    {
        $connection = Connection::connect();
        $reviewCount = $connection
            ->createQueryBuilder()
            ->select('COUNT("rating")')
            ->from("reviews")
            ->where('apartment_id =?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        return $reviewCount;
    }

    public function getReviewSum(int $apartmentId): array
    {
        $connection = Connection::connect();
        $reviewSum = $connection
            ->createQueryBuilder()
            ->select('rating')
            ->from("reviews")
            ->where('apartment_id =?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAllAssociative();

        return $reviewSum;
    }

    public function checkIfLeftReview(int $apartmentId, int $userId): bool
    {
        $review = Connection::connect()
            ->createQueryBuilder()
            ->select('user_id', 'apartment_id')
            ->from('reviews')
            ->where('apartment_id=?')
            ->andWhere('user_id=?')
            ->setParameter(0, $apartmentId)
            ->setParameter(1, $userId)
            ->executeQuery()
            ->fetchAssociative();

        return !empty($review);
    }
}
