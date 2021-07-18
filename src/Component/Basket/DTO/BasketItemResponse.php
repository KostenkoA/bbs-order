<?php

namespace App\Component\Basket\DTO;

class BasketItemResponse
{
    /** @var BasketItemDiscountResponse[]|null */
    public $discounts;

    /** @var BasketItemBonusResponse[]|null */
    public $bonuses;

    /** @var string */
    public $nomenclatureId;

    /** @var int */
    public $quantity;

    /** @var float|int */
    public $price;

    /** @var float|int */
    public $cost;

    /** @var float|int */
    public $bonusAmount;

    /** @var float|int */
    public $discountAmount;
}
