<?php

namespace App\Component\UserService\Request;

use App\Component\UserService\Response\BonusProfile;
use App\Component\UserService\Response\UserServiceError;
use App\Component\UserService\UserServiceException;
use App\Component\UserService\UserServiceRequestInterface;
use App\Component\UserService\UserServiceResponseException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use GuzzleHttp\RequestOptions;
use App\Security\User;

class BonusProfileRequest extends RequestAbstract implements UserServiceRequestInterface
{
    protected const REQUEST_METHOD = 'GET';

    /**
     * @var User
     */
    private $user;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * BonusProfileRequest constructor.
     * @param string $dsn
     * @param SerializerInterface $serializer
     * @param JWTEncoderInterface $jwtEncoder
     */
    public function __construct(
        string $dsn,
        SerializerInterface $serializer,
        JWTEncoderInterface $jwtEncoder
    ) {
        $this->jwtEncoder = $jwtEncoder;
        $this->serializer = $serializer;

        parent::__construct($dsn);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    protected function getRequestOptions(): array
    {
        return [
            RequestOptions::HEADERS => [
                'Authorization' => sprintf('Bearer %s', $this->generateJwt()),
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getActionUri(): string
    {
        return '/public/bonus';
    }

    /**
     * @return string
     */
    private function generateJwt(): string
    {
        try {
            return $this->jwtEncoder->encode(
                [
                    'id' => $this->user->getId(),
                    'roles' => $this->user->getRoles(),
                ]
            );
        } catch (JWTEncodeFailureException $e) {
            return '';
        }
    }

    /**
     * @return BonusProfile
     * @throws UserServiceException
     * @throws UserServiceResponseException
     */
    public function handleResponse(): BonusProfile
    {
        if ($response = $this->getResponse()) {
            $responseContent = $response->getBody()->getContents();

            if ($response->getStatusCode() !== 200) {
                try {
                    $errorModel = $this->serializer->deserialize($responseContent, UserServiceError::class, 'json');
                } catch (NotEncodableValueException $exception) {
                    throw new UserServiceResponseException(sprintf('Wrong response for %s ', static::class));
                }
                $errorModel->httpCode = $response->getStatusCode();

                throw new UserServiceResponseException(
                    $errorModel->error,
                    $errorModel->httpCode,
                    null,
                    $errorModel->code,
                    $errorModel->errors ?? []
                );
            }

            /** @var BonusProfile $model */
            $model = $this->serializer->deserialize($responseContent, BonusProfile::class, 'json');

            return $model;
        }

        throw new UserServiceException('Bonus response is empty');
    }
}
