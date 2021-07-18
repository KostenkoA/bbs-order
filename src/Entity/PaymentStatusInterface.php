<?php

namespace App\Entity;

interface PaymentStatusInterface
{
    /**
     * is new payment
     */
    public const STATUS_NEW = 0;

    /**
     * created by payment method
     */
    public const STATUS_CREATED = 1;

    /**
     * is in processing
     */
    public const STATUS_PROCESSING = 2;

    /**
     * payment approved
     */
    public const STATUS_APPROVED = 3;

    /**
     * payment declined
     */
    public const STATUS_DECLINED = 4;

    /**
     * time for payment is expired
     */
    public const STATUS_EXPIRED = 5;

    /**
     * successful transaction has been fully or partially canceled
     */
    public const STATUS_REVERSED = 6;

    /**
     * undefined
     */
    public const STATUS_UNDEFINED = 7;

    /**
     * a refund has been generated but not yet processed
     */
    public const REVERSE_STATUS_CREATED = 0;

    /**
     * the refund was rejected by the FONDY payment gateway, an external payment system, or
     */
    public const REVERSE_STATUS_APPROVED = 1;

    /**
     * return successfully completed
     */
    public const REVERSE_STATUS_DECLINED = 2;
}
