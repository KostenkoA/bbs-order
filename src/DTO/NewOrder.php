<?php

namespace App\DTO;

use App\DTO\Basket\BasketChosenGiftItem;
use App\DTO\Basket\BasketItem;
use App\DTO\Subscription\SubscriptionItem;

class NewOrder
{
    /** @var string */
    public $project;

    /** @var string */
    public $userRef;

    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $middleName;

    /** @var string */
    public $phone;

    /** @var string */
    public $email;

    /** @var integer */
    public $deliveryType;

    /** @var integer */
    public $paymentType;

    /** @var string */
    public $comment;

    /** @var int */
    public $callBack = 1;

    /** @var BasketItem[] */
    public $orderItems = [];

    /** @var integer */
    public $deliveryCarrier;

    /** @var string */
    public $region;

    /** @var string */
    public $district;

    /** @var string|null */
    public $cityRef;

    /** @var string */
    public $city;

    /** @var integer */
    public $streetType;

    /** @var string */
    public $streetTypeName;

    /** @var string|null */
    public $streetRef;

    /** @var string */
    public $street;

    /** @var string */
    public $building;

    /** @var string */
    public $apartment;

    /** @var string|null */
    public $deliveryBranchRef;

    /** @var string */
    public $deliveryBranch;

    /** @var string */
    public $deliveryShop;

    /** @var integer */
    public $userLanguageId;

    /** @var int */
    public $usedBonuses;

    /** @var string[]|null */
    public $certificates = [];

    /** @var BasketItem[] */
    public $items;

    /** @var SubscriptionItem|null */
    public $subscriptionItems = [];

    /** @var BasketChosenGiftItem[]  */
    public $chosenGiftItems = [];
}
