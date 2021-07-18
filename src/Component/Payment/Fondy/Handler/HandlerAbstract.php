<?php

namespace App\Component\Payment\Fondy\Handler;

use App\Interfaces\HttpRequestHandleAbstract;
use App\Component\Payment\Fondy\FondyException;
use App\Component\Payment\Fondy\Response\ResponseAbstract;
use Exception;

abstract class HandlerAbstract extends HttpRequestHandleAbstract
{
    /**
     * @return ResponseAbstract
     * @throws FondyException
     */
    public function handle()
    {
        $this->checkErrors();

        return $this->buildResponseDTO();
    }

    /**
     * @throws FondyException
     */
    public function checkErrors(): void
    {
        $this->checkResponseContent();
        $this->checkResponseModel();
    }

    /**
     * @throws FondyException
     * @throws Exception
     */
    public function checkResponseContent(): void
    {
        $data = $this->jsonDecode();

        if (empty($data['response']) || !is_array($data['response'])) {
            $this->throwError('response empty', 500);
        }
    }

    /**
     * @throws FondyException
     */
    public function checkResponseModel(): void
    {
        $responseDTO = $this->buildResponseDTO();

        if ($responseDTO->response_status !== ResponseAbstract::STATUS_SUCCESS) {
            $this->throwError(
                sprintf(
                    'Fondy response error%s: "%s : %s"',
                    property_exists($responseDTO, 'request_id') ? sprintf(
                        ' (request_id : %s)',
                        $responseDTO->request_id
                    ) : '',
                    $responseDTO->error_code,
                    $responseDTO->error_message
                ),
                500
            );
        }
    }

    /**
     * @param string $message
     * @param integer $code
     * @throws FondyException
     */
    protected function throwError(string $message, int $code): void
    {
        throw new FondyException($message, $code);
    }

    /**
     * @return ResponseAbstract
     */
    abstract protected function buildResponseDTO();
}
