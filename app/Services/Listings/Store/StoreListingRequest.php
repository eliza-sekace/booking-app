<?php

namespace App\Services\Listings\Store;

class StoreListingRequest
{
    private int $user_id;
    private string $name;
    private string $address;
    private string $description;
    private string $available_from;
    private ?string $available_till;
    private ?string $imgPath;
    private int $price;

    public function __construct($user_id, $name, $address, $description, $available_from, $available_till=null, $imgPath = null, $price)
    {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->address = $address;
        $this->description = $description;
        $this->available_from = $available_from;
        $this->available_till = $available_till;
        $this->imgPath = $imgPath;
        $this->price = $price;
    }

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