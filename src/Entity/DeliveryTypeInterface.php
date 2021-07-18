<?php

namespace App\Entity;

interface DeliveryTypeInterface
{
    public const DELIVERY_BRANCH = 1;

    public const DELIVERY_ADDRESS = 2;

    public const DELIVERY_SHOP = 3;

    /**
     * @return integer
     */
    public function getDeliveryType(): ?int;
}
