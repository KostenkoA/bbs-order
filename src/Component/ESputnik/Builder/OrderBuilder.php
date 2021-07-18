<?php

namespace App\Component\ESputnik\Builder;

use App\DTO\ESputnik\OrderDTO;
use App\DTO\ESputnik\OrderItemDTO;
use App\DTO\ESputnik\OrdersDTO;
use App\Entity\Order;

class OrderBuilder
{
    /**
     * @var string
     */
    private $productBaseUrl;

    /**
     * @var string
     */
    private $productImageBaseUrl;

    public function __construct(string $productBaseUrl, string $productImageBaseUrl)
    {
        $this->productBaseUrl = $productBaseUrl;
        $this->productImageBaseUrl = $productImageBaseUrl;
    }

    /**
     * @param Order $order
     * @return OrdersDTO
     */
    public function buildOrderESputnikDTO(Order $order): OrdersDTO
    {
        $ordersDTO = new OrdersDTO();
        $ordersDTO->orders[] = $this->buildESputnikDTO($order);

        return $ordersDTO;
    }

    /**
     * @param Order $orderEntity
     * @return OrderDTO
     */
    private function buildESputnikDTO(Order $orderEntity): OrderDTO
    {
        $order = new OrderDTO();

        $order->externalOrderId = (string)$orderEntity->getNumber();
        $order->externalCustomerId = (string)$orderEntity->getUserRef();
        $order->totalCost = (float)$orderEntity->getCost();
        $order->status = (string)$order->getOrderStatus($orderEntity->getStatus());
        $order->date = (string)$orderEntity->getCreatedAt()->format('c');
        $order->email = (string)$orderEntity->getEmail();
        $order->phone = (string)$orderEntity->getPhone();
        $order->firstName = (string)$orderEntity->getFirstName();
        $order->lastName = (string)$orderEntity->getLastName();
        $order->shipping = (float)$orderEntity->getDeliveryPrice();
        $order->discount = (float)$orderEntity->getBonusDiscountAmount() + (float)$orderEntity->getDiscountAmount();
        $order->storeId = (float)$orderEntity->getProjectName();
        $order->deliveryMethod = (string)$order->getESputnikDeliveryMethod($orderEntity->getDeliveryType());
        $order->paymentMethod = (string)$order->getESputnikPaymentMethod($orderEntity->getPaymentType());
        $order->deliveryAddress = sprintf('%s - %s:%s', $orderEntity->getCity(), $orderEntity->getDistrict(), $orderEntity->getStreet());

        foreach ($orderEntity->getOrderItems() as $item) {
            $orderItem = new OrderItemDTO();
            $orderItem->externalItemId = (string)$item->getProductId();
            $orderItem->name = (string)$item->getTitleUkr();
            $orderItem->category = !empty($item->getCategory()) ? $item->getCategory()['titleUkr'] : ' ';
            $orderItem->quantity = (integer)$item->getQuantity();
            $orderItem->cost = (float)$item->getPrice();
            $orderItem->url = sprintf('%s%s', $this->productBaseUrl, $item->getSlug());
            $orderItem->imageUrl = !empty($item->getImages()) ? sprintf('%s%s', $this->productImageBaseUrl, $item->getImages()[0]['link']) : ' ';
            $order->items[] = $orderItem;
        }

        return $order;
    }
}
