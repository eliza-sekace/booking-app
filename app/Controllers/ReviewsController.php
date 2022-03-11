<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Database\Connection;

use App\Redirect;

class ReviewsController
{
    public function __construct()
    {
        //session_start();
        if (!isset($_SESSION['user_id'])) {
            header("location: /login", true);
        }
    }

    public function create($vars)
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

        $reviews = [];
        $reviews[] = $_POST['review'];

        $review = $connection
            ->createQueryBuilder()
            ->select('user_id','apartment_id')
            ->from('reviews')
            ->where('apartment_id=?')
            ->andWhere('user_id=?')
            ->setParameter(0, $vars["id"])
            ->setParameter(1, $_SESSION['user_id'])
            ->executeQuery()
            ->fetchAssociative();

        if ($review == false) {
            $connection
                ->insert('reviews', [
                    'user_id' => $_SESSION['user_id'],
                    'apartment_id' => $result['id'],
                    'review' => $_POST['review'],
                    'rating' => $_POST['rating']
                ]);
        }

        return new Redirect("/listings/{$vars["id"]}");
    }

}

