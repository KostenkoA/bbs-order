<?php

namespace App\Component\Payment\Fondy\Handler;

use App\Component\Payment\Fondy\Response\NewPaymentResponse;
use Exception;

class PaymentHandler extends HandlerAbstract
{
    /**
     * @return NewPaymentResponse
     * @throws Exception
     */
    protected function buildResponseDTO(): NewPaymentResponse
    {
        $data = $this->jsonDecode();

        /** @var NewPaymentResponse $model */
        $model = $this->denormalizer->denormalize($data['response'] ?? [], NewPaymentResponse::class);

        return $model;
    }
}
