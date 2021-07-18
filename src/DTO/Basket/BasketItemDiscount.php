<?php

namespace App\DTO\Basket;

class BasketItemDiscount
{
    /** @var string */
    public $title;

    /** @var float */
    public $amount;

    public function __construct(string $title, float $amount)
    {
        $this->title = $title;
        $this->amount = $amount;
    }
}
