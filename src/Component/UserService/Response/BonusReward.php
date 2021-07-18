<?php

namespace App\Component\UserService\Response;

class BonusReward
{
    /**
     * @var string
     */
    public $levelName;

    /**
     * @var string
     */
    public $levelNameRu;

    /**
     * @var float
     */
    public $amountFrom;

    /**
     * @var float
     */
    public $amountTo;

    /**
     * @var float
     */
    public $rate;

    /**
     * @var bool
     */
    public $pointsNeverExpire;

    /**
     * @var bool
     */
    public $birthdayGift;

    /**
     * @var bool
     */
    public $birthdayCoupon;

    /**
     * @var bool
     */
    public $freeDelivery;
}
