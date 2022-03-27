<?php
namespace App\Repositories\Reservations;


interface ReservationRepository
{
    public function getAvailableDates(int $apartmentId):array;
    public function getAllReservations(int $apartmentId):array;
    public function store(int $userId, int $apartmentId, string $reserveFrom, string $reserveTill):void;
}
