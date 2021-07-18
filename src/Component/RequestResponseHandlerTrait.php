<?php


namespace App\Component;

use Exception;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * Trait RequestResponseHandlerTrait
 *
 * @method Response getResponse
 *
 * @package App\Component
 */
trait RequestResponseHandlerTrait
{
    /**
     * @return mixed
     * @throws RequestResponseException
     */
    public function handleResponse()
    {
        if ($response = $this->getResponse()) {
            $responseContent = $response->getBody()->getContents();

            if ($response->getStatusCode() !== 200) {
                try {
                    /** @var RequestResponseError $errorModel */
                    $errorModel = $this->serializer->deserialize($responseContent, RequestResponseError::class, 'json');
                } catch (NotEncodableValueException $exception) {
                    throw new RequestResponseException(sprintf('Wrong response for %s ', static::class));
                }
                $errorModel->httpCode = $response->getStatusCode();

                throw new RequestResponseException(
                    $errorModel->error,
                    $errorModel->httpCode,
                    null,
                    $errorModel->code,
                    $errorModel->errors ?? []
                );
            }

            try {
                $model = $this->handleSuccess($responseContent);
            } catch (Exception $exception) {
                throw  new RequestResponseException($exception->getMessage(), 500);
            }

            return $model;
        }

        throw new RequestResponseException('Response is empty');
    }

    /**
     * @param $responseContent
     * @return mixed
     * @throws RequestResponseException
     */
    protected function handleSuccess($responseContent)
    {
        throw new RequestResponseException('success response not handled');
    }
}
