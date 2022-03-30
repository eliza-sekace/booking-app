<?php

namespace App\Controllers;

use App\Redirect;
use App\Repositories\Reservations\PdoReservationRepository;
use App\Services\Listings\Show\ShowListingService;
use App\Services\Reservations\Store\StoreReservationRequest;
use App\Services\Reservations\Store\StoreReservationService;
use App\Views\View;
use Psr\Container\ContainerInterface;

class ReservationsController
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
        return new View("Reservations/create.html", [
            'listingId' => $vars['id']
        ]);
    }

    public function store($vars)
    {
        $service =  $this->container->get(StoreReservationService::class);;
        $apartmentId = $vars['id'];
        $reserveFrom = $_POST['reserve_from'];
        $reserveTill = $_POST['reserve_till'];
        $userId = $_SESSION['user_id'];

        if ($service->execute(new StoreReservationRequest($apartmentId), $reserveFrom, $reserveTill)){
            $service->store($userId, $apartmentId, $reserveFrom, $reserveTill);
        }
        return new Redirect("/listings/{$apartmentId}");
    }
}