<?php

namespace App\Component\UserService;

use App\Component\UserService\Request\BonusProfileRequest;
use App\Component\UserService\Response\BonusProfile;
use App\DTO\User;
use App\DTO\UserFind;
use App\Security\User as AuthUser;
use App\Producer\UserProducer;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Component\UserService\Request\RegistrationRequest;

class UserServiceComponent
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var ContainerInterface
     */
    private $requestLocator;

    /**
     * @var UserProducer
     */
    private $userProducer;

    /**
     * UserServiceComponent constructor.
     * @param ContainerInterface $requestLocator
     * @param NormalizerInterface $normalizer
     * @param UserProducer $userProducer
     */
    public function __construct(
        ContainerInterface $requestLocator,
        NormalizerInterface $normalizer,
        UserProducer $userProducer
    ) {
        $this->requestLocator = $requestLocator;
        $this->normalizer = $normalizer;
        $this->userProducer = $userProducer;
    }

    /**
     * @param $class
     * @return UserServiceRequestInterface
     * @throws UserServiceException
     */
    private function getRequest($class): UserServiceRequestInterface
    {
        if (!$this->requestLocator->has($class)) {
            throw new UserServiceException('Can\'t found request by class ' . $class);
        }
        /** @var UserServiceRequestInterface $action */
        $action = $this->requestLocator->get($class);

        return $action;
    }

    /**
     * @param User $user
     * @return bool
     * @throws UserServiceException
     */
    public function register(User $user): bool
    {
        /** @var RegistrationRequest $request */
        $request = $this->getRequest(RegistrationRequest::class);

        $request->setUserData($this->normalizer->normalize($user, 'array', ['groups' => ['registration']]))
            ->send();

        return $request->getResponse() && $request->getResponse()->getStatusCode() === 201;
    }

    /**
     * @param User $user
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendForAttach(User $user): void
    {
        $dto = new UserFind();
        $props = get_object_vars($user);

        foreach ($props as $prop => $value) {
            if (property_exists(UserFind::class, $prop)) {
                $dto->{$prop} = $value;
            }
        }

        $this->userProducer->sendForAttach($dto);
    }

    /**
     * @param AuthUser $user
     * @return BonusProfile
     * @throws UserServiceException
     * @throws UserServiceResponseException
     */
    public function getBonusProfile(AuthUser $user): BonusProfile
    {
        /** @var BonusProfileRequest $request */
        $request = $this->getRequest(BonusProfileRequest::class);
        $request->setUser($user);
        $request->send();

        return $request->handleResponse();
    }
}
