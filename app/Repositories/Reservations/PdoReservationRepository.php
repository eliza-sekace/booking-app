<?php
namespace App\Repositories\Reservations;

use App\Database\Connection;
use App\Repositories\Reservations\ReservationRepository;

class PdoReservationRepository implements ReservationRepository
{
    public function getAvailableDates(int $apartmentId): array
    {
        $connection=Connection::connect();
        $availableDates = $connection
            ->createQueryBuilder()
            ->select('id', 'available_from', 'available_till')
            ->from('apartments')
            ->where('id=?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        return $availableDates;
    }

    public function getAllReservations(int $apartmentId): array
    {
        $connection = Connection::connect();
        $allReservations = $connection
            ->createQueryBuilder()
            ->select('reserve_from', 'reserve_till')
            ->from('reservations')
            ->where('apartment_id=?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAllAssociative();

        return $allReservations;
    }

    public function store(int $userId, int $apartmentId, string $reserveFrom, string $reserveTill): void
    {
        Connection::connect()
            ->insert('reservations', [
                'user_id' => $userId,
                'apartment_id' => $apartmentId,
                'reserve_from' => $reserveFrom,
                'reserve_till' => $reserveTill,
            ]);
    }
}
