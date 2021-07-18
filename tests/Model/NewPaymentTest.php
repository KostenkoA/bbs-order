<?php

namespace App\Tests\Model;

use App\Component\Payment\NewPaymentInterface;

class NewPaymentTest implements NewPaymentInterface
{
    public function getPaymentUrl()
    {
        return 'new-payment-redirect-url';
    }
}
