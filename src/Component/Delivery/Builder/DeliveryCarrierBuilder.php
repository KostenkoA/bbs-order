<?php


namespace App\Component\Delivery\Builder;

use App\Component\Delivery\DeliveryCarrierInterface;
use App\Component\Delivery\Factory\DeliveryCarrierFactory;
use App\Component\Delivery\Factory\DeliveryTypeFactory;
use App\Component\Delivery\Model\DeliveryOrderModel;
use App\Component\Delivery\DeliveryException;

class DeliveryCarrierBuilder
{
    /**
     * @var DeliveryTypeFactory
     */
    private $deliveryTypeFactory;

    /**
     * @var DeliveryCarrierFactory
     */
    private $carrierFactory;


    /**
     * DeliveryComponent constructor.
     * @param DeliveryTypeFactory $deliveryTypeFactory
     * @param DeliveryCarrierFactory $carrierFactory
     */
    public function __construct(
        DeliveryTypeFactory $deliveryTypeFactory,
        DeliveryCarrierFactory $carrierFactory
    ) {
        $this->deliveryTypeFactory = $deliveryTypeFactory;
        $this->carrierFactory = $carrierFactory;
    }

    /**
     * @param DeliveryOrderModel $deliveryOrderModel
     * @return DeliveryCarrierInterface
     * @throws DeliveryException
     */
    public function buildDeliveryCarrier(DeliveryOrderModel $deliveryOrderModel): DeliveryCarrierInterface
    {
        $deliveryTypeModel = $this->deliveryTypeFactory->create($deliveryOrderModel->deliveryType);
        $carrierModel = $this->carrierFactory->create($deliveryOrderModel->carrierType);

        if (!$carrierModel->checkDeliveryTypeEnable($deliveryTypeModel)) {
            throw new DeliveryException(
                sprintf(
                    'Carrier %s not enable for deliveryType %s',
                    $deliveryOrderModel->carrierType,
                    $deliveryOrderModel->deliveryType
                )
            );
        }

        $carrierModel->setDeliveryType($deliveryTypeModel);
        $carrierModel->setDeliveryOrder($deliveryOrderModel);

        return $carrierModel;
    }
}
