<?php

namespace App\DTO\Basket;

class BasketItemBonus
{
    /** @var string */
    public $title;

    /** @var float */
    public $points;

    /** @var string */
    public $accrualDate;

    public function __construct(string $title, float $points, string $accrualDate)
    {
        $this->title = $title;
        $this->points = $points;
        $this->accrualDate = $accrualDate;
    }
}
