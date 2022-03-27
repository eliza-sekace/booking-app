<?php

namespace App\Services\Listings\Store;

use Carbon\Carbon;

class StoreListingRequest
{
   // private int $id;
    private int $user_id;
    private string $name;
    private string $address;
    private string $description;
    private string $available_from;
    private ?string $available_till;
    private ?string $imgPath;
    private int $price;


    public function __construct($user_id, $name, $address, $description,  $price, $available_from, $available_till, $imgPath = null)
    {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->address = $address;
        $this->description = $description;
        $this->available_from = empty($available_from)? Carbon::now()->format('Y-m-d'):$available_from;
        $this->available_till = empty($available_till)? null:$available_from;
        $this->imgPath = $imgPath;
        $this->price = $price;

    }

//    public function getId(): mixed
//    {
//        return $this->id;
//    }

    public function getUserId():int
    {
        return $this->user_id;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getAddress():string
    {
        return $this->address;
    }

    public function getDescription():string
    {
        return $this->description;
    }

    public function getAvailableFrom():string
    {
        return $this->available_from;
    }

    public function getAvailableTill(): ?string
    {
        return $this->available_till;
    }

    public function getImgPath(): ?string
    {
        return $this->imgPath;
    }

    public function getPrice() :int
    {
        return $this->price;
    }
}