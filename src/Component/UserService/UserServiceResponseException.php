<?php

namespace App\Component\UserService;

use App\Exception\AvailableErrorsExceptionInterface;
use App\Exception\AvailableJsonExceptionInterface;
use App\Exception\NoSentryExceptionInterface;
use Exception;
use Throwable;

class UserServiceResponseException extends Exception implements AvailableJsonExceptionInterface, AvailableErrorsExceptionInterface, NoSentryExceptionInterface
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * BonusResponseException constructor.
     * @param string $message
     * @param int $httpCode
     * @param Throwable|null $previous
     * @param int $statusCode
     * @param array $errors
     */
    public function __construct(
        $message = '',
        $httpCode = 500,
        Throwable $previous = null,
        int $statusCode = 1501,
        array $errors = []
    ) {
        $this->errors = $errors;
        $this->statusCode = $statusCode;

        parent::__construct($message, $httpCode, $previous);
    }

    /**
     * @return int
     */
    public function getHttpError(): int
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
