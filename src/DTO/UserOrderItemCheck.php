<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class UserOrderItemCheck
{
    /**
     * @var string
     * @Groups({"send"})
     */
    public $project;

    /**
     * @var string
     * @Groups({"send"})
     */
    public $productSlug;

    /**
     * @var int|string
     * @Groups({"send"})
     */
    public $userId;

    /**
     * @var bool|null
     * @Groups({"send"})
     */
    public $exist;

    /**
     * @var string
     */
    public $queue;

    /**
     * @var string
     */
    public $processor;
}
