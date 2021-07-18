<?php

namespace App\Component\Payment\Fondy\Response;

use App\Component\Payment\NewPaymentInterface;
use Symfony\Component\Serializer\Annotation as Serializer;

class NewPaymentResponse extends ResponseAbstract implements NewPaymentInterface
{
    /**
     * @var string
     */
    public $request_id;

    /**
     * @var string
     */
    public $payment_id;

    /**
     * @var string
     */
    public $checkout_url;

    /**
     * @Serializer\Groups({"new-payment.response"})
     * @return string
     */
    public function getPaymentUrl(): string
    {
        return $this->checkout_url;
    }
}
