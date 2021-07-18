<?php

namespace App\Component\Payment;

interface NewPaymentInterface
{
    /**
     * @return string
     */
    public function getPaymentUrl();
}
