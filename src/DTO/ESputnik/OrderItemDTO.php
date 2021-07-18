<?php

namespace App\DTO\ESputnik;

class OrderItemDTO
{
    /**
     * @var string
     */
    public $externalItemId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $category;

    /**
     * @var integer
     */
    public $quantity;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $imageUrl;

    /**
     * @var string
     */
    public $description;
}
