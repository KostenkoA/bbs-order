<?php

namespace App\Component\Delivery\Carrier;

use App\Component\Delivery\DeliveryTypeInterface;
use App\Component\Delivery\Type\Shop;

class WithoutCarrier extends AbstractCarrier
{
    public function checkDeliveryTypeEnable(DeliveryTypeInterface $deliveryType): bool
    {
        return $deliveryType instanceof Shop;
    }
}
