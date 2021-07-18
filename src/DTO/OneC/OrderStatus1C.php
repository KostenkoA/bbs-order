<?php

namespace App\DTO\OneC;

class OrderStatus1C
{
    public const STATUS_NEW = 1;

    public const STATUS_AGREED = 2;

    public const STATUS_TO_DELIVERY = 3;

    public const STATUS_TRANSIT = 4;

    public const STATUS_CANCELED = 5;

    public const STATUS_COMPLETED = 6;

    public const STATUS_PARTIAL_COLLECTED = 8;

    public const STATUS_COLLECTED = 9;

    public const ENABLE_STATUSES = [
        self::STATUS_NEW => self::STATUS_NEW,
        self::STATUS_AGREED => self::STATUS_AGREED,
        self::STATUS_TO_DELIVERY => self::STATUS_TO_DELIVERY,
        self::STATUS_TRANSIT => self::STATUS_TRANSIT,
        self::STATUS_CANCELED => self::STATUS_CANCELED,
        self::STATUS_COMPLETED => self::STATUS_COMPLETED,
        self::STATUS_PARTIAL_COLLECTED => self::STATUS_PARTIAL_COLLECTED,
        self::STATUS_COLLECTED => self::STATUS_COLLECTED,
    ];

    /** @var string */
    public $ref = '';

    /** @var string */
    private $externalRef = '';

    /** @var int */
    public $status = 0;

    /** @var string */
    public $statusTransport = '';

    /** @var string */
    public $declarationNumber = '';

    /** @var bool */
    public $success;

    /** @var string */
    public $error;

    /** @var integer */
    public $errorCode;

    /** @var OrderStatus1CProduct[]|null */
    public $products;

    /**
     * @param string $value
     */
    public function setExternalRef(string $value)
    {
        //TODO: временно отключено, изменение параметра externalRef в 1c
//        $this->externalRef = preg_replace('/[^\d.]/', '', $value);
        $this->externalRef = $value;
    }

    /**
     * @return string
     */
    public function getExternalRef(): string
    {
        return $this->externalRef;
    }
}
