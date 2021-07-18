<?php

namespace App\Component\Delivery;

use App\Component\Delivery\Response\DeliverySearchError;
use App\Component\Delivery\Response\SettlementResponse;
use App\Interfaces\HttpRequestInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

abstract class DeliveryRequestAbstract implements HttpRequestInterface
{
    protected const REQUEST_METHOD = 'GET';

    /**
     * @var string;
     */
    protected $dsn;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ResponseInterface|null
     */
    protected $response;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * RequestAbstract constructor.
     * @param string $dsn
     * @param SerializerInterface $serializer
     */
    public function __construct(string $dsn, SerializerInterface $serializer)
    {
        $this->dsn = $dsn;
        $this->client = new Client();
        $this->serializer = $serializer;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return void
     * @throws DeliverySearchException
     */
    public function send(): void
    {
        try {
            $this->response = $this->client->request(
                static::REQUEST_METHOD,
                sprintf(
                    '%s%s',
                    $this->dsn,
                    $this->getActionUri()
                ),
                $this->getRequestOptions()
            );
        } catch (ConnectException $exception) {
            throw new DeliverySearchException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        } catch (RequestException $exception) {
            $this->response = $exception->getResponse();
        } catch (GuzzleException $exception) {
            throw new DeliverySearchException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }

    /**
     * @return SettlementResponse
     * @throws DeliverySearchException
     * @throws DeliverySearchResponseException
     */
    public function handleResponse(): SettlementResponse
    {
        if ($response = $this->getResponse()) {
            $this->handleError($response);
            try {
                /** @var SettlementResponse $model */
                $model = $this->serializer->deserialize($response->getBody()->getContents(), SettlementResponse::class, 'json');
            } catch (Exception $exception) {
                throw  new DeliverySearchResponseException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getPrevious());
            }

            return $model;
        }

        throw new DeliverySearchException('Warehouse response is empty');
    }

    /**
     * @param ResponseInterface $response
     * @throws DeliverySearchResponseException
     */
    protected function handleError(ResponseInterface $response)
    {
        if ($response && $response->getStatusCode() !== Response::HTTP_OK) {
            try {
                /** @var DeliverySearchError $errorModel */
                $errorModel = $this->serializer->deserialize($response->getBody()->getContents(), DeliverySearchError::class, 'json');
            } catch (NotEncodableValueException $exception) {
                throw new DeliverySearchResponseException(sprintf('Wrong response for %s ', static::class));
            }
            $errorModel->httpCode = $response->getStatusCode();

            throw new DeliverySearchResponseException(
                $errorModel->error,
                $errorModel->httpCode,
                null,
                $errorModel->code,
                $errorModel->errors ?? []
            );
        }
    }

    /**
     * @return string[]
     */
    abstract protected function getRequestOptions(): array;

    /**
     * @return string
     */
    abstract protected function getActionUri(): string;
}
