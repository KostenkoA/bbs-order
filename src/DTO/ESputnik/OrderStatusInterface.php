<?php

namespace App\DTO\ESputnik;

interface OrderStatusInterface
{
    public const INITIALIZED_TYPE = 'INITIALIZED',
                 IN_PROGRESS_TYPE = 'IN_PROGRESS',
                 DELIVERED_TYPE = 'DELIVERED',
                 CANCELLED_TYPE = 'CANCELLED',
                 ABANDONED_SHOPPING_CART_TYPE = 'ABANDONED_SHOPPING_CART';

    public const STATUS_NEW = 0,
                 STATUS_IN_PROGRESS = 2,
                 STATUS_TO_DELIVERY = 3,
                 STATUS_CANCELED = 5;
}
