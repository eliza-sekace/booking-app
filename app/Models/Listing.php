<?php

namespace App\Models;

class Listing
{
    private ?int $id;
    private int $user_id;
    private string $name;
    private string $address;
    private string $description;
    private ?string $available_from;
    private ?string $available_till;
    private ?string $imgPath;
    private int $price;


    public function __construct(?int $id, $user_id, $name, $address, $description, $available_from, $available_till = null, $imgPath = null, $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->description = $description;
        $this->available_from = $available_from;
        $this->available_till = $available_till;
        $this->user_id = $user_id;
        $this->imgPath = $imgPath;
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAvailableFrom(): string
    {
        return $this->available_from;
    }

    public function getAvailableTill()
    {
        return $this->available_till;
    }

    /**
     * @return string
     */
    public function getImgPath(): ?string
    {
        return $this->imgPath;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public static function make($attributes)
    {
        if (!isset($attributes['id'])
            || !isset($attributes['user_id'])
            || !isset($attributes['name'])
            || !isset($attributes['address'])
            || !isset($attributes['description'])
            || !isset($attributes['price'])
        ) {
            return null;
        }

        return new self(
            $attributes['id'],
            $attributes['user_id'],
            $attributes['name'],
            $attributes['address'],
            $attributes['description'],
            $attributes['available_from'],
            $attributes['available_till'],
            $attributes['img_path'],
            $attributes['price']
        );
    }


}