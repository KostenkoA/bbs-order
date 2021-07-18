<?php

namespace App\Component\Payment;

use App\Component\Payment\Model\Card;

interface CardInterface
{
    public function getCard(): ?Card;
}
