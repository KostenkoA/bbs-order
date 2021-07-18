<?php

namespace App\Component\Payment\Fondy\Handler;

use App\Component\Payment\Fondy\Response\ReverseResponse;
use Exception;

class ReverseHandler extends HandlerAbstract
{
    /**
     * @return ReverseResponse
     * @throws Exception
     */
    protected function buildResponseDTO(): ReverseResponse
    {
        $data = $this->jsonDecode();

        /** @var ReverseResponse $model */
        $model = $this->denormalizer->denormalize($data['response'] ?? [], ReverseResponse::class);

        $model->error_code = $model->response_code ?? null;
        $model->error_message = $model->response_description ?? null;

        return $model;
    }
}
