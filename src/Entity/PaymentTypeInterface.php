<?php

namespace App\Entity;

interface PaymentTypeInterface
{
    public const PAYMENT_CASH = 1;

    public const PAYMENT_CARD = 2;

    public const PAYMENT_CARD_SHOP = 3;

    /**
     * @return null|integer
     */
    public function getPaymentType(): ?int;
}
