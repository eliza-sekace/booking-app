<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Redirect;
use App\Views\View;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
        $connection = Connection::connect();
        //get all apartment reservations
        $reservations = $connection
            ->createQueryBuilder()
            ->select('reserve_from', 'reserve_till')
            ->from('reservations')
            ->where('apartment_id=?')
            ->setParameter(0, $vars['id'])
            ->executeQuery()
            ->fetchAllAssociative();

        //parbaudam, vai ievadits pareizi (piem 03.03-01.03)
        //parbaudam, vai datumi pieejami/briivi:
        //kkaa ar pacinu vai neparklajas.
        //carbon-parse carbon period
        $startDate = Carbon::parse($_POST['reserve_from'])->toDateString();
        $endDate = Carbon::parse($_POST['reserve_till'])->toDateString();
        //get date periods
        $period = CarbonPeriod::between($startDate, $endDate);
        $overlapCheck = false;
        foreach ($reservations as $reservation) {
            $check=$period->overlaps(
                Carbon::parse($reservation['reserve_from']),
                Carbon::parse($reservation['reserve_till'])
            );
            if ($check == true){
                $overlapCheck = true;
            }
        }

        $betweenAvailable= false;
        //parbaudam vai velamie dat sakrit ar available
        //$availableDates = ReservationsRepository::class->getAvailable();
        $availableDates = $connection
            ->createQueryBuilder()
            ->select('available_from', 'available_till')
            ->from('apartments')
            ->where('id=?')
            ->setParameter(0, $vars['id'])
            ->executeQuery()
            ->fetchAssociative();

      $betweenAvailable=false;
            if (Carbon::parse($availableDates['available_from'])->lte(Carbon::parse($_POST['reserve_from'])) &&
                Carbon::parse($availableDates['available_till'])->gte(Carbon::parse($_POST['reserve_till']))){
                $betweenAvailable = true;
            }
            $reservationStatus='';
        if (Carbon::parse($_POST['reserve_till'])->gt(Carbon::parse($_POST['reserve_from']))
        && $overlapCheck == false && $betweenAvailable==true) {
            $connection
                ->insert('reservations', [
                    'user_id' => $_SESSION['user_id'],
                    'apartment_id' => $vars['id'],
                    'reserve_from' => $_POST['reserve_from'],
                    'reserve_till' => $_POST['reserve_till'],
                ]);
           $reservationStatus="Reservation successful!";
        } else $reservationStatus= "Sorry, these dates are not available!";

       return new Redirect("/listings/{$vars['id']}");

    }
}