<?php

namespace App\Repositories\Listing;

use App\Models\Listing;

interface ListingRepository
{
    public function getById(int $id):?Listing;
    public function save(Listing $listing):void;
    public function index():array;
}