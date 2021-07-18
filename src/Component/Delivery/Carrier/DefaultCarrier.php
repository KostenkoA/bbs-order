<?php

namespace App\Component\Delivery\Carrier;

use App\Component\Delivery\DeliveryTypeInterface;
use App\Component\Delivery\Type\Address;

class DefaultCarrier extends AbstractCarrier
{
    /**
     * @var float
     */
    private $deliveryPrice;

    /**
     * @var float
     */
    private $freeDeliveryOrderPrice;

    /**
     * WithoutCarrier constructor.
     * @param float $freeDeliveryOrderPrice
     * @param float $deliveryPrice
     */
    public function __construct(float $freeDeliveryOrderPrice, float $deliveryPrice)
    {
        $this->freeDeliveryOrderPrice = $freeDeliveryOrderPrice;
        $this->deliveryPrice = $deliveryPrice;
    }

    /**
     * @param DeliveryTypeInterface $deliveryType
     * @return bool
     */
    public function checkDeliveryTypeEnable(DeliveryTypeInterface $deliveryType): bool
    {
        return $deliveryType instanceof Address;
    }

    /**
     * @return float
     */
    public function getDeliveryPrice(): float
    {
        $price = 0;

        if ($this->deliveryType instanceof Address) {
            $price = $this->deliveryOrder->orderPrice <= $this->freeDeliveryOrderPrice ? $this->deliveryPrice : 0;
        }

        return $price;
    }
}
