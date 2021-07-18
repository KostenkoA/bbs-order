<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation as Serializer;

class UserFind extends User
{
    /**
     * @var string
     * @Serializer\Groups({"attach"})
     */
    public $queue;

    /**
     * @var string
     * @Serializer\Groups({"attach"})
     */
    public $processor;
}
