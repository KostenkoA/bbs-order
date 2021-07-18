<?php

namespace App\Component\Payment;

interface CheckoutPaymentInterface
{
    public function getPaymentStatus(): int;
}
