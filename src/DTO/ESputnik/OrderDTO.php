<?php

namespace App\DTO\ESputnik;

use App\Entity\DeliveryTypeInterface;
use App\Entity\PaymentTypeInterface;

class OrderDTO implements OrderStatusInterface, DeliveryTypeInterface, PaymentTypeInterface
{
    public const CURRENCY_TYPE = 'UAH';

    /**
     * @var string
     */
    public $externalOrderId;

    /**
     * @var string
     */
    public $externalCustomerId;

    /**
     * @var float
     */
    public $totalCost;

    /**
     * @var string
     */
    public $status = self::INITIALIZED_TYPE;

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $currency = self::CURRENCY_TYPE; // TODO is currency one

    /**
     * @var float
     */
    public $shipping;

    /**
     * @var float
     */
    public $discount;

    /**
     * @var string
     */
    public $storeId;

    /**
     * @var string
     */
    public $source;

    /**
     * @var string
     */
    public $deliveryMethod;

    /**
     * @var string
     */
    public $paymentMethod;

    /**
     * @var string
     */
    public $deliveryAddress;

    /**
     * @var OrderItemDTO[]
     */
    public $items = [];

    /**
     * @param int $status
     * @return string
     */
    public function getOrderStatus(int $status): string
    {
        $statuses = [
            self::STATUS_NEW => self::INITIALIZED_TYPE,
            self::STATUS_IN_PROGRESS => self::IN_PROGRESS_TYPE,
            self::STATUS_TO_DELIVERY => self::DELIVERED_TYPE,
            self::STATUS_CANCELED => self::CANCELLED_TYPE,
        ];

        return $statuses[$status] ?? $statuses[self::STATUS_NEW];
    }

    /**
     * @param int $deliveryType
     * @return string
     */
    public function getESputnikDeliveryMethod(int $deliveryType): string
    {
        $methods = [
            self::DELIVERY_BRANCH => 'Branch',
            self::DELIVERY_ADDRESS => 'Address',
            self::DELIVERY_SHOP => 'Shop',
        ];

        return $methods[$deliveryType] ?? $methods[self::DELIVERY_BRANCH];
    }

    /**
     * @param int $paymentType
     * @return string
     */
    public function getESputnikPaymentMethod(int $paymentType): string
    {
        $methods = [
            self::PAYMENT_CASH => 'Cash',
            self::PAYMENT_CARD => 'Card',
            self::PAYMENT_CARD_SHOP => 'CardInShop',
        ];

        return $methods[$paymentType] ?? $methods[self::PAYMENT_CASH];
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryType(): ?int
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentType(): ?int
    {
        return null;
    }
}
