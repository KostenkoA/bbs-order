<?php

namespace App\DTO\OneC;

use Symfony\Component\Serializer\Annotation as Serializer;

class OrderStatusError1C
{
    /**
     * @var string
     */
    public $ref;

    /**
     * @var string
     */
    public $externalRef;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $errorDescription;

    /**
     * @var string|integer
     * @Serializer\Groups({"save"})
     */
    public $errorCode;
}
