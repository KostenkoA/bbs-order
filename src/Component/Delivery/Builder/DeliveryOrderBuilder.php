<?php

namespace App\Component\Delivery\Builder;

use App\Component\Delivery\Model\DeliveryOrderModel;
use App\Entity\Order;

class DeliveryOrderBuilder
{
    /**
     * @param Order $order
     * @return DeliveryOrderModel
     */
    public function buildDeliveryModel(Order $order): DeliveryOrderModel
    {
        $model = new DeliveryOrderModel();

        $model->carrierType = $order->getDeliveryCarrier();
        $model->deliveryType = $order->getDeliveryType();
        $model->orderPrice = $order->getPrice();

        return $model;
    }
}
