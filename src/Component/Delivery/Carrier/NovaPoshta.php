<?php

namespace App\Component\Delivery\Carrier;

use App\Component\Delivery\DeliveryTypeInterface;
use App\Component\Delivery\Type\Address;
use App\Component\Delivery\Type\Branch;

class NovaPoshta extends AbstractCarrier
{
    /**
     * @var float
     */
    private $deliveryPrice;

    /**
     * NovaPoshta constructor.
     * @param float $deliveryPrice
     */
    public function __construct(float $deliveryPrice)
    {
        $this->deliveryPrice = $deliveryPrice;
    }

    /**
     * @param DeliveryTypeInterface $deliveryType
     * @return bool
     */
    public function checkDeliveryTypeEnable(DeliveryTypeInterface $deliveryType): bool
    {
        return $deliveryType instanceof Address || $deliveryType instanceof Branch;
    }

    /**
     * @return float
     */
    public function getDeliveryPrice(): float
    {
        return $this->deliveryPrice;
    }
}
