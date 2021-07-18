<?php

namespace App\DTO\Subscription;

use App\DTO\Basket\BasketItem;
use DateTime;

class SubscriptionItem extends BasketItem
{
    /** @var string */
    public $internalId;

    /** @var integer */
    public $quantity;

    /** @var integer */
    public $intervalDays;

    /** @var DateTime */
    public $startDate;

    /** @var DateTime|null */
    public $skipDateFrom;

    /** @var DateTime|null */
    public $skipDateTo;

    /** @var integer */
    public $isActive;
}
