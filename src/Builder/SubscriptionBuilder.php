<?php

namespace App\Builder;

use App\DTO\Subscription\Subscription as SubscriptionDTO;
use App\DTO\Subscription\SubscriptionItem as SubscriptionItemDto;
use App\Entity\Order;
use App\Entity\Subscription;
use DateInterval;
use DateTime;
use Exception;

class SubscriptionBuilder
{
    /**
     * @param Subscription $subscription
     * @param Order $order
     * @param SubscriptionItemDto[] $subscriptionItems
     * @return Subscription
     */
    public function fillFromOrder(Subscription $subscription, Order $order, array $subscriptionItems = []): Subscription
    {
        $dto = new SubscriptionDTO();

        $dto->firstName = $order->getFirstName();
        $dto->lastName = $order->getLastName();
        $dto->middleName = $order->getMiddleName();
        $dto->phone = $order->getPhone();
        $dto->email = $order->getEmail();
        $dto->deliveryType = $order->getDeliveryType();
        $dto->deliveryCarrier = $order->getDeliveryCarrier();
        $dto->region = $order->getRegion();
        $dto->district = $order->getDistrict();
        $dto->city = $order->getCity();
        $dto->cityRef = $order->getCityRef();
        $dto->streetType = $order->getStreetType();
        $dto->street = $order->getStreet();
        $dto->streetRef = $order->getStreetRef();
        $dto->building = $order->getBuilding();
        $dto->apartment = $order->getApartment();
        $dto->deliveryBranch = $order->getDeliveryBranch();
        $dto->deliveryBranchRef = $order->getDeliveryBranchRef();
        $dto->deliveryShop = $order->getDeliveryShop();
        $dto->paymentType = $order->getPaymentType();

        $subscription->updateFromDto($dto);

        foreach ($subscriptionItems as $subscriptionItem) {
            $date = $subscriptionItem->startDate;
            if ($date === null) {
                $date = new DateTime();
                try {
                    $date->add(new DateInterval(sprintf('P%dD', $subscriptionItem->intervalDays)));
                } catch (Exception $exception) {
                }
            }

            $subscriptionItem->startDate = $date;
            $subscriptionItem->isActive = true;

            $subscription->updateOrAddItem($subscriptionItem);
        }

        return $subscription;
    }
}
