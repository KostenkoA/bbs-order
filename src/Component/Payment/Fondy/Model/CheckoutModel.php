<?php

namespace App\Component\Payment\Fondy\Model;

use Symfony\Component\Serializer\Annotation as Serializer;

class CheckoutModel extends AbstractModel
{
    /**
     * @var string
     * @Serializer\Groups({"signature", "request"})
     */
    public $orderId = '';
}
