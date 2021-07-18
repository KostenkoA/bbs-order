<?php

namespace App\Entity;

interface DeliveryCarrierInterface
{
    public const CARRIER_DEFAULT_CARRIER = 0;

    public const CARRIER_MEEST_EXPRESS = 1;

    public const CARRIER_NOVA_POSHTA = 2;

    public const CARRIER_EASY_POST = 3;

    public const CARRIER_SMART_POST = 4;

    public const CARRIER_JUSTIN = 5;

    public const CARRIER_IPOST = 6;

    /**
     * @return int|null
     */
    public function getDeliveryCarrier(): ?int;
}
