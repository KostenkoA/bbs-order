<?php

namespace App\DTO\OneC;

use DateTimeInterface;

class Order1C
{
    /** @var string */
    public $ref = '';

    /** @var string */
    public $externalRef = '';

    /** @var DateTimeInterface */
    public $date;

    /** @var integer */
    public $number = '';

    /** @var string */
    public $customer = '';

    /** @var string */
    public $firstName = '';

    /** @var string */
    public $secondName = '';

    /** @var string */
    public $middleName = '';

    /** @var string */
    public $telephone = '';

    /** @var string */
    public $email = '';

    /** @var string */
    public $region = '';

    /** @var string */
    public $district = '';

    /** @var string */
    public $city = '';

    /** @var string */
    public $street = '';

    /** @var string */
    public $building = '';

    /** @var string */
    public $apartment = '';

    /** @var string */
    public $branch = '';

    /** @var string */
    public $shop = '';

    /** @var int */
    public $streetType = 0;

    /** @var DateTimeInterface|null */
    public $shippingDate;

    /** @var int */
    public $shipmentType = 0;

    /** @var int */
    public $carrier = 0;

    /** @var float */
    public $deliveryCost = 0.0;

    /** @var float */
    public $paymentByVouchers = 0.0;

    /** @var int */
    public $paymentByBonuses = 0;

    /** @var bool */
    public $callback = false;

    /** @var string */
    public $comment = '';

    /** @var string[] */
    public $promotion = [];

    /** @var string[] */
    public $certificates = [];

    /** @var string */
    public $project = '';

    /** @var OrderProduct1C[] */
    public $products = [];

    /** @var OrderGift1C[] */
    public $gifts;
}
