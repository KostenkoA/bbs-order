<?php

namespace App\Service\User;

use App\Component\UserService\UserServiceComponent;
use App\Component\UserService\UserServiceException;
use App\DTO\User;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;

class UserService
{
    /**
     * @var UserServiceComponent
     */
    private $userComponent;

    /**
     * UserService constructor.
     * @param UserServiceComponent $userComponent
     */
    public function __construct(UserServiceComponent $userComponent)
    {
        $this->userComponent = $userComponent;
    }

    /**
     * @param User $user
     * @throws UserServiceException
     */
    public function register(User $user): void
    {
        $this->userComponent->register($user);
    }

    /**
     * @param User $user
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendForAttach(User $user): void
    {
        $this->userComponent->sendForAttach($user);
    }
}
