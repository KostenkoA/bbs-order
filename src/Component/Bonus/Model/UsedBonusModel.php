<?php

namespace App\Component\Bonus\Model;

class UsedBonusModel
{
    /**
     * @var float
     */
    private $discountAmount;

    /**
     * @var int
     */
    private $bonusPoints;

    /**
     * CombinedUsedBonus constructor.
     * @param int $bonusPoints
     * @param float $discountAmount
     */
    public function __construct(int $bonusPoints, float $discountAmount)
    {
        $this->discountAmount = $discountAmount;
        $this->bonusPoints = $bonusPoints;
    }

    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    /**
     * @return int
     */
    public function getBonusPoints(): int
    {
        return $this->bonusPoints;
    }
}
