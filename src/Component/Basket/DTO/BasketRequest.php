<?php

namespace App\Component\Basket\DTO;

class BasketRequest
{
    /** @var string|null */
    public $phone;

    /** @var string|null */
    public $customerRef;

    /** @var int|null */
    public $paymentByBonuses;

    /** @var BasketItemRequest[] */
    public $basketItems = [];

    /** @var string[]|null */
    public $certificates = [];
}
