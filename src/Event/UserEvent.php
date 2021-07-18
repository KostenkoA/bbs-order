<?php

namespace App\Event;

use App\DTO\User;
use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event
{
    public const EVENT_USER_REGISTRATION = 'app.user.registration';

    public const EVENT_USER_SEND_FOR_ATTACH = 'app.user.send-for-attach';

    /**
     * @var User
     */
    protected $userDTO;

    public function __construct(User $userDTO)
    {
        $this->userDTO = $userDTO;
    }

    public function getUser(): User
    {
        return $this->userDTO;
    }
}
