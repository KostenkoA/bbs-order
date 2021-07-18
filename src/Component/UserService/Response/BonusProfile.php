<?php

namespace App\Component\UserService\Response;

class BonusProfile
{
    /**
     * Amount of active bonus
     *
     * @var float
     */
    public $activePoints;

    /**
     * Active bonus dates
     *
     * @var string[]
     */
    public $activePointsDates;

    /**
     * Amount of passive bonus
     *
     * @var float
     */
    public $passivePoints;

    /**
     * @var string[]
     */
    public $passivePointsDates;

    /**
     * Current level name
     *
     * @var string
     */
    public $currentLevelName;

    /**
     * Current level name(Ru)
     *
     * @var string
     */
    public $currentLevelNameRu;

    /**
     * Current level validity date
     *
     * @var string
     */
    public $currentLevelDuration;

    /**
     * Current bonus accrual rate
     *
     * @var float
     */
    public $currentRate;

    /**
     * Current amount for customer
     *
     * @var float
     */
    public $currentAmount;

    /**
     * @var BonusReward[]
     */
    public $rewards;

    /**
     * @var BonusConversionRule[]
     */
    public $conversionRules;
}
