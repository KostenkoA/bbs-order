<?php

namespace App\Entity;

interface ItemReserveStateInterface
{
    public const STATUS_NEW = 0;

    public const STATUS_RESERVED = 1;

    public const STATUS_CANCELED = 2;

    public const STATUS_DELIVERY_WAITING = 3;

    public const STATUS_TRANSFERRED = 4;

    public const STATUS_REFUSED = 5;

    /**
     * @return integer
     */
    public function getReserveState(): ?int;
}
