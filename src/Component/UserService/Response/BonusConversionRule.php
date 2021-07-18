<?php

namespace App\Component\UserService\Response;

class BonusConversionRule
{
    /**
     * @var float
     */
    public $points;

    /**
     * @var float
     */
    public $cash;

    /**
     * BonusProfileConversionRule constructor.
     * @param float|null $points
     * @param float|null $cash
     */
    public function __construct(?float $points = null, ?float $cash = null)
    {
        $this->points = $points ?? 0.0;
        $this->cash = $cash ?? 0.0;
    }
}
