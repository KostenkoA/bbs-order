<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation as Serializer;

class User
{
    /**
     * @var string
     * @Serializer\Groups({"registration"})
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $middleName;

    /**
     * @var string
     * @Serializer\Groups({"registration","attach"})
     */
    public $phone;

    /**
     * @var string
     */
    public $email;

    /**
     * @var integer
     * @Serializer\Groups({"registration"})
     */
    public $language;

    /**
     * @var string
     */
    public $project;
}
