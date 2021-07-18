<?php

namespace App\Component\Basket\DTO;

class BasketItemBonusResponse
{
    /** @var string */
    public $id;

    /** @var string */
    public $title;

    /** @var float|int */
    public $points;

    /** @var string */
    public $accrualDate;
}
