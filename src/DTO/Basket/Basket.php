<?php

namespace App\DTO\Basket;

class Basket
{
    /** @var string|null */
    public $project;

    /** @var BasketItem[] */
    public $basketItems;

    /** @var integer */
    public $bonus;

    /** @var string */
    public $phone;

    /** @var string[]|null */
    public $certificates = [];
}
