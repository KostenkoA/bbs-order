<?php

namespace App\Component\Payment\Fondy\Request;

use App\Component\Payment\Fondy\Model\AbstractModel;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Interfaces\HttpRequestInterface;

class RequestAbstract implements HttpRequestInterface
{
    protected const ACTION = '';

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @var ResponseInterface|null
     */
    protected $response;

    /**
     * @var AbstractModel|null
     */
    protected $model;

    /**
     * PaymentRequest constructor.
     * @param ClientInterface $client
     * @param NormalizerInterface $normalizer
     * @param string $baseUri
     */
    public function __construct(ClientInterface $client, NormalizerInterface $normalizer, string $baseUri)
    {
        $this->httpClient = $client;
        $this->normalizer = $normalizer;
        $this->baseUri = $baseUri;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return AbstractModel|null
     */
    public function getModel(): ?AbstractModel
    {
        return $this->model;
    }

    /**
     * @param AbstractModel|null $model
     * @return $this
     */
    public function setModel(AbstractModel $model = null): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return rtrim($this->baseUri, '/') . static::ACTION;
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function send(): void
    {
        try {
            $response = $this->httpClient->send(
                $this->buildRequestModel()
            );
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        }

        $this->response = $response;
    }

    /**
     * @return Request
     */
    protected function buildRequestModel(): Request
    {
        return new Request(
            'POST',
            $this->getUri(),
            ['Content-Type' => 'application/json'],
            json_encode(
                [
                    'request' => $this->normalizer->normalize($this->getModel(), 'array', ['groups' => ['request']]),
                ]
            )
        );
    }
}
