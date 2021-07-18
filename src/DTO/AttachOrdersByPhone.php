<?php

namespace App\DTO;

class AttachOrdersByPhone
{
    /**
     * @var string
     */
    public $phone;

    /**
     * @var string|integer
     */
    public $user_id;

    /**
     * @var string|null
     */
    public $first_name;

    /**
     * @var string|null
     */
    public $last_name;

    /**
     * @var string|null
     */
    public $middle_name;

    /**
     * @var string
     */
    public $project;
}
