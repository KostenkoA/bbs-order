<?php

namespace App\Component\Payment\Fondy\Model;

class ReverseModel extends AbstractModel
{
    /**
     * @var string
     * @Serializer\Groups({"signature", "request"})
     */
    public $orderId = '';

    /**
     * @var integer
     * @Serializer\Groups({"signature","request"})
     */
    public $amount = 0;

    /**
     * @var string
     * @Serializer\Groups({"signature","request"})
     */
    public $currency = '';

    /**
     * @var string
     * @Serializer\Groups({"signature","request"})
     */
    public $comment = '';
}
