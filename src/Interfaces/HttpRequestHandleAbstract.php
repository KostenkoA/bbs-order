<?php

namespace App\Interfaces;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Exception;

abstract class HttpRequestHandleAbstract
{
    /**
     * @var string
     */
    protected $responseContent = '';

    /**
     * @var int
     */
    protected $responseStatusCode = 200;

    /**
     * @var DenormalizerInterface
     */
    protected $denormalizer;

    /**
     * HttpRequestHandleAbstract constructor.
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * @return string
     */
    public function getResponseContent(): string
    {
        return $this->responseContent;
    }

    /**
     * @return int
     */
    public function getResponseStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    /**
     * @param string $responseContent
     * @return void
     */
    public function setResponseContent(string $responseContent): void
    {
        $this->responseContent = $responseContent;
    }

    /**
     * @param int $responseStatusCode
     * @return void
     */
    public function setResponseStatusCode(int $responseStatusCode): void
    {
        $this->responseStatusCode = $responseStatusCode;
    }

    /**
     * @return array|null
     * @throws Exception
     */
    protected function jsonDecode(): ?array
    {
        $data = json_decode($this->getResponseContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->throwError(json_last_error_msg(), json_last_error());
        }

        return $data;
    }

    /**
     * @return mixed
     */
    abstract public function handle();

    /**
     * @return void
     */
    abstract public function checkErrors(): void;

    /**
     * @param string $message
     * @param integer $code
     * @throws Exception;
     */
    abstract protected function throwError(string $message, int $code): void;
}
