<?php

namespace App\Component\Delivery;

use App\Component\Delivery\Builder\DeliveryCarrierBuilder;
use App\Component\Delivery\Builder\DeliveryOrderBuilder;
use App\Component\Delivery\Request\StreetSearchRequest;
use App\Component\Delivery\Request\WarehouseSearchRequest;
use App\Component\Delivery\Response\SettlementResponse;
use App\Entity\DeliveryCarrierInterface as DeliveryCarrierType;
use App\Entity\Order;
use Psr\Container\ContainerInterface;

class DeliveryComponent
{
    /**
     * @var DeliveryOrderBuilder
     */
    private $orderBuilder;

    /**
     * @var DeliveryCarrierBuilder
     */
    private $carrierBuilder;

    /**
     * @var ContainerInterface
     */
    private $requestLocator;

    /**
     * DeliveryComponent constructor.
     * @param DeliveryOrderBuilder $orderBuilder
     * @param DeliveryCarrierBuilder $carrierBuilder
     * @param ContainerInterface $requestLocator
     */
    public function __construct(
        DeliveryOrderBuilder $orderBuilder,
        DeliveryCarrierBuilder $carrierBuilder,
        ContainerInterface $requestLocator
    ) {
        $this->orderBuilder = $orderBuilder;
        $this->carrierBuilder = $carrierBuilder;
        $this->requestLocator = $requestLocator;
    }

    /**
     * @param Order $order
     * @return float
     * @throws DeliveryException
     */
    public function calculateDeliveryPrice(Order $order): float
    {
        $deliveryModel = $this->orderBuilder->buildDeliveryModel($order);
        $carrier = $this->carrierBuilder->buildDeliveryCarrier($deliveryModel);

        return $carrier->getDeliveryPrice();
    }

    /**
     * @param $class
     * @return DeliveryRequestInterface
     * @throws DeliverySearchException
     */
    private function getRequest($class): DeliveryRequestInterface
    {
        if (!$this->requestLocator->has($class)) {
            throw new DeliverySearchException('Can\'t found request by class ' . $class);
        }
        /** @var DeliveryRequestInterface $action */
        $action = $this->requestLocator->get($class);

        return $action;
    }

    /**
     * @param string $settlementId
     * @param int $type
     * @param string|null $id
     * @return SettlementResponse
     * @throws DeliverySearchException
     * @throws DeliverySearchResponseException
     */
    public function getWarehouse(string $settlementId, int $type, ?string $id = null): SettlementResponse
    {
        /** @var WarehouseSearchRequest $request */
        $request = $this->getRequest(WarehouseSearchRequest::class);
        $request->setSettlementId($settlementId);
        $request->setType($this->getWarehouseType($type));
        $request->setId($id);
        $request->send();

        return $request->handleResponse();
    }

    /**
     * @param string $settlementId
     * @param string|null $id
     * @return SettlementResponse
     * @throws DeliverySearchException
     * @throws DeliverySearchResponseException
     */
    public function getStreet(string $settlementId, ?string $id = null): SettlementResponse
    {
        /** @var StreetSearchRequest $request */
        $request = $this->getRequest(StreetSearchRequest::class);
        $request->setSettlementId($settlementId);
        $request->setId($id);
        $request->send();

        return $request->handleResponse();
    }

    /**
     * @param int $type
     * @return string
     */
    private function getWarehouseType(int $type): string
    {
        switch ($type) {
            case DeliveryCarrierType::CARRIER_JUSTIN:
                return DeliveryRequestInterface::CARRIER_JUSTIN_TYPE;
            default:
                return DeliveryRequestInterface::CARRIER_NOVA_POSHTA_TYPE;
        }
    }
}
