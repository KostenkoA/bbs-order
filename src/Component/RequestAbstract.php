<?php

namespace App\Component;

use App\Interfaces\HttpRequestInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

abstract class RequestAbstract implements HttpRequestInterface
{
    protected const REQUEST_METHOD = 'POST';

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
     * RequestAbstract constructor.
     * @param string $dsn
     */
    public function __construct(string $dsn)
    {
        $this->dsn = $dsn;
        $this->client = new Client();
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
     * @throws GuzzleException
     */
    public function send(): void
    {
        $this->response = $this->client->request(
            static::REQUEST_METHOD,
            sprintf(
                '%s%s',
                $this->dsn,
                $this->getActionUri()
            ),
            $this->getRequestOptions()
        );
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
