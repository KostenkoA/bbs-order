<?php

namespace App\DTO\OneC;

use DateTime;

class OrderStatus1CProduct
{
    public const STATUS_NEW = 0;

    public const STATUS_RESERVE = 1;

    public const STATUS_CANCEL = 2;

    public const STATUS_DELIVERY = 3;

    public const STATUS_TRANSFERRED = 4;

    public const STATUS_REFUSE = 5;


    /** @var string */
    public $ref = '';

    /** @var int */
    public $quantity = 0;

    /** @var int */
    public $reserveState = 0;

    /** @var DateTime|null */
    public $deliveryDate;
}
