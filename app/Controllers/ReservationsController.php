<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Redirect;
use App\Repositories\Reservations\PdoReservationRepository;
use App\Services\Reservations\Store\StoreReservationRequest;
use App\Services\Reservations\Store\StoreReservationService;
use App\Views\View;

class ReservationsController
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("location: /login", true);
        }
    }

    public function create($vars)
    {
        return new View("Reservations/create.html", [
            'listingId' => $vars['id']
        ]);
    }

    public function store($vars)
    {
        $repository = new PdoReservationRepository();
        $service = new StoreReservationService($repository);
        $apartmentId = $vars['id'];
        $reserveFrom = $_POST['reserve_from'];
        $reserveTill = $_POST['reserve_till'];

        if ($service->execute(new StoreReservationRequest($apartmentId), $reserveFrom, $reserveTill)){
            Connection::connect()
                ->insert('reservations', [
                    'user_id' => $_SESSION['user_id'],
                    'apartment_id' => $apartmentId,
                    'reserve_from' => $_POST['reserve_from'],
                    'reserve_till' => $_POST['reserve_till'],
                ]);
        }
        return new Redirect("/listings/{$apartmentId}");
    }
}