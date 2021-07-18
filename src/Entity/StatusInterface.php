<?php

namespace App\Entity;

interface StatusInterface
{
    public const STATUS_NEW = 0;

    public const STATUS_REGISTERED = 1;

    public const STATUS_IN_PROGRESS = 2;

    public const STATUS_TO_DELIVERY = 3;

    public const STATUS_TRANSIT = 4;

    public const STATUS_CANCELED = 5;

    public const STATUS_COMPLETED = 6; //Статус который долен появиться.

    public const STATUS_ERROR_FROM_1C = 7;

    public const STATUS_PARTIAL_COLLECTED = 8;

    public const STATUS_COLLECTED = 9;

    /**
     * @return integer
     */
    public function getStatus(): ?int;
}
