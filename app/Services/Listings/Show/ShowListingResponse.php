<?php
namespace App\Services\Listings\Show;

use App\Models\Listing;

class ShowListingResponse
{
    private Listing $listing;
    private array $reviews;
    private string $minDate;
    private array $reservedDates;
    private array $availableDates;
    private $reviewCount;
    private $reviewSum;

    public function __construct(Listing $listing, array $reviews, string $minDate,array $availableDates, array $reservedDates,
     $reviewCount, $reviewSum)
{
    $this->listing = $listing;
    $this->reviews = $reviews;
    $this->minDate = $minDate;
    $this->reservedDates = $reservedDates;
    $this->availableDates = $availableDates;
    $this->reviewCount = $reviewCount;
    $this->reviewSum = $reviewSum;
}

    public function getListing(): Listing
    {
        return $this->listing;
    }

    public function getReviews(): array
    {
        return $this->reviews;
    }

    public function getMinDate(): string
    {
        return $this->minDate;
    }

    public function getReservedDates(): array
    {
        return $this->reservedDates;
    }

    public function getAvailableDates(): array
    {
        return $this->availableDates;
    }

    public function getReviewCount()
    {
        return $this->reviewCount;
    }

    public function getReviewSum()
    {
        return $this->reviewSum;
    }
}