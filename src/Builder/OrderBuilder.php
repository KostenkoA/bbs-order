<?php

namespace App\Builder;

use App\DTO\Basket\BasketItem;
use App\DTO\NewOrder;
use App\DTO\OneC\OrderGift1C;
use App\DTO\OneC\Order1C;
use App\DTO\OneC\OrderProduct1C;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Subscription;
use DateTime;

class OrderBuilder
{
    /**
     * @param Order $orderEntity
     * @return Order1C
     */
    public function build1CDTO(Order $orderEntity): Order1C
    {
        $order = new Order1C();

        $order->ref = '';
        $order->externalRef = (string)$orderEntity->getHash();
        $order->date = $orderEntity->getCreatedAt() ? $orderEntity->getCreatedAt()->format(
            'Y-m-d\TH:i:s'
        ) : '';
        $order->number = (integer)$orderEntity->getNumber();
        $order->firstName = (string)$orderEntity->getFirstName();
        $order->secondName = (string)$orderEntity->getLastName();
        $order->middleName = (string)$orderEntity->getMiddleName();
        $order->telephone = (string)$orderEntity->getPhone();
        $order->email = (string)$orderEntity->getEmail();
        $order->region = (string)($orderEntity->getRegionUkr() ?? $orderEntity->getRegion());
        $order->district = (string)($orderEntity->getDistrictUkr() ?? $orderEntity->getDistrict());
        $order->city = (string)($orderEntity->getCityUkr() ?? $orderEntity->getCity());
        $order->street = (string)($orderEntity->getStreetUkr() ?? $orderEntity->getStreet());
        $order->building = (string)$orderEntity->getBuilding();
        $order->apartment = (string)$orderEntity->getApartment();
        $order->streetType = (integer)$orderEntity->getStreetType();
        $order->shippingDate = null;
        $order->shipmentType = (integer)$orderEntity->getDeliveryType();
        $order->branch = (string)($orderEntity->getDeliveryBranchUkr() ?? $orderEntity->getDeliveryBranch());
        $order->shop = (string)$orderEntity->getDeliveryShop();
        $order->carrier = (integer)$orderEntity->getDeliveryCarrier();
        $order->deliveryCost = (float)$orderEntity->getDeliveryPrice();
        $order->paymentByBonuses = (int)$orderEntity->getUsedBonuses();
        $order->callback = (bool)$orderEntity->getCallBack();
        $order->comment = (string)$orderEntity->getComment();
        $order->project = (string)$orderEntity->getProjectName();
        $order->certificates = $orderEntity->getCertificates();
        $order->promotion = [];
        $order->products = [];
        $order->gifts = [];

        $orderEntity->getOrderItems()->forAll(
            function ($index, OrderItem $orderItemEntity) use ($order) {
                if (!$orderItemEntity->getIsGift()) {
                    $product = new OrderProduct1C();
                    $product->nomenclature = $orderItemEntity->getInternalId();
                    $product->quantity = $orderItemEntity->getQuantity();
                    $product->price = $orderItemEntity->getPrice();
                    $product->amount = $orderItemEntity->getTotalPrice();

                    $order->products[] = $product;
                } else {
                    $gift = new OrderGift1C();
                    $gift->discount = $orderItemEntity->getGiftDiscount() ?
                        $orderItemEntity->getGiftDiscount()->getDiscountRef() : null;
                    $gift->nomenclature = $orderItemEntity->getInternalId();
                    $gift->quantity = $orderItemEntity->getQuantity();

                    $order->gifts[] = $gift;
                }


                return $orderItemEntity;
            }
        );

        return $order;
    }

    public function buildNewOrderFromSubscription(Subscription $subscription, DateTime $date): NewOrder
    {
        $dto = new NewOrder();
        $dto->firstName = $subscription->getFirstName();
        $dto->lastName = $subscription->getLastName();
        $dto->middleName = $subscription->getMiddleName();
        $dto->phone = $subscription->getPhone();
        $dto->email = $subscription->getEmail();
        $dto->deliveryType = $subscription->getDeliveryType();
        $dto->deliveryCarrier = $subscription->getDeliveryCarrier();
        $dto->region = $subscription->getRegion();
        $dto->district = $subscription->getDistrict();
        $dto->city = $subscription->getCity();
        $dto->streetType = $subscription->getStreetType();
        $dto->street = $subscription->getStreet();
        $dto->building = $subscription->getBuilding();
        $dto->apartment = $subscription->getApartment();
        $dto->deliveryBranch = $subscription->getDeliveryBranch();
        $dto->deliveryShop = $subscription->getDeliveryShop();
        $dto->cityRef = $subscription->getCityRef();
        $dto->deliveryBranchRef = $subscription->getDeliveryBranchRef();
        $dto->streetRef = $subscription->getStreetRef();
        $dto->paymentType = $subscription->getPaymentType();
        $dto->comment = sprintf('Заказ по подписке(на %s)', $date->format('d.m.Y'));

        foreach ($subscription->getSubscriptionItems() as $subscriptionItem) {
            if ($subscriptionItem->isEnableForDate($date)) {
                $basketItem = new BasketItem();
                $basketItem->forSubscription = true;
                $basketItem->internalId = $subscriptionItem->getInternalId();
                $basketItem->quantity = $subscriptionItem->getQuantity();
                $dto->orderItems[] = $basketItem;
            }
        }

        return $dto;
    }
}
