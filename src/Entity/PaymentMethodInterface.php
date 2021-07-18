<?php

namespace App\Entity;

interface PaymentMethodInterface
{
    public const FONDY_METHOD = 0;

    public function getMethod(): int;
}
