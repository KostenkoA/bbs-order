<?php

namespace App\EventListener;

use App\Component\UserService\UserServiceException;
use App\Event\UserEvent;
use App\Service\User\UserService;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;

class UserListener
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserRegistrationListener constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param UserEvent $event
     * @throws UserServiceException
     */
    public function register(UserEvent $event): void
    {
        $this->userService->register($event->getUser());
    }

    /**
     * @param UserEvent $event
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendForAttach(UserEvent $event): void
    {
        $this->userService->sendForAttach($event->getUser());
    }
}
