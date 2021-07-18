<?php

namespace App\Component\ESputnik\Action;

use App\Component\ESputnik\ESputnikActionInterface;
use App\Component\ESputnik\Response\ESputnikError;
use App\Component\RequestAbstract;
use App\DTO\ESputnik\OrdersDTO;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class SendOrdersAction extends RequestAbstract implements ESputnikActionInterface
{
    /**
     * @var OrdersDTO
     */
    private $requestData;

    /**
     * @var array
     */
    private $oauthData;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(string $dsn, array $oauthData, SerializerInterface $serializer)
    {
        parent::__construct($dsn);
        $this->oauthData = $oauthData;
        $this->serializer = $serializer;
    }

    /**
     * @return bool
     * @throws ESputnikError
     */
    public function handlerResult(): bool
    {
        $this->handleError();

        if ($this->getResponse()) {
            return true;
        }

        throw new ESputnikError(sprintf('Empty bonus action(%s) response', static::class));
    }

    /**
     * @throws ESputnikError
     */
    private function handleError(): void
    {
        $response = $this->getResponse();

        if ($response && $response->getStatusCode() !== Response::HTTP_OK) {
            throw new ESputnikError($response->getBody(), $response->getStatusCode());
        }

        if (!$this->response) {
            throw new ESputnikError(sprintf('Empty eSputnik action(%s) response', static::class));
        }
    }

    /**
     * @inheritDoc
     */
    protected function getRequestOptions(): array
    {
        return array_merge(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                RequestOptions::TIMEOUT => 10,
                RequestOptions::AUTH => [$this->oauthData['user_name'], $this->oauthData['password']],
            ],
            [
                'body' => $this->serializer->serialize($this->requestData, 'json'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function getActionUri(): string
    {
        return 'orders';
    }

    /**
     * @param OrdersDTO $ordersDTO
     * @return bool
     * @throws ESputnikError
     * @throws GuzzleException
     */
    public function sendAction(OrdersDTO $ordersDTO): bool
    {
        $this->requestData = $ordersDTO;

        $this->send();

        return $this->handlerResult();
    }
}
