<?php

namespace App\DTO\Subscription;

use App\DTO\Basket\BasketItem;
use DateTime;

class Subscription
{
    /** @var string|null */
    public $userRef;

    /** @var string */
    public $project;

    /** @var SubscriptionItem[] */
    public $items;

    /** @var integer */
    public $isActive;

    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string|null */
    public $middleName;

    /** @var string|null */
    public $phone;

    /** @var string|null */
    public $email;

    /** @var integer */
    public $deliveryType;

    /** @var integer|null */
    public $deliveryCarrier;

    /** @var string|null */
    public $region;

    /** @var string|null */
    public $district;

    /** @var string|null */
    public $city;

    /** @var int|null */
    public $streetType;

    /** @var string */
    public $streetTypeName;

    /** @var string|null */
    public $street;

    /** @var string|null */
    public $building;

    /** @var string|null */
    public $apartment;

    /** @var string|null */
    public $deliveryBranch;

    /** @var string|null */
    public $deliveryShop;

    /** @var int|null */
    public $cityRef;

    /** @var string|null */
    public $deliveryBranchRef;

    /** @var string|null */
    public $streetRef;

    /** @var integer */
    public $paymentType;
}
