<?php

namespace App\DTO\Basket;

class BasketItem
{
    /** @var string */
    public $internalId;

    /** @var bool */
    public $forSubscription = false;

    /** @var integer */
    public $quantity;

    /** @var integer */
    public $availableQuantity;

    /** @var float|null */
    public $recommendedPrice;

    /** @var float */
    public $sellingPrice;

    /** @var integer */
    public $errorCode;

    /** @var integer */
    public $bonus;
}
