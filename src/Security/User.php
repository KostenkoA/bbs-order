<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

final class User implements JWTUserInterface
{
    public const ROLE_SERVICE = 'ROLE_SERVICE';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $roles;


    /**
     * User constructor.
     * @param $id
     * @param $roles
     */
    public function __construct(string $id, ?array $roles = null)
    {
        $this->id = $id;
        $this->roles = $roles ?? [];
    }

    /**
     * @param string $id
     * @param array $payload
     * @return User|JWTUserInterface
     */
    public static function createFromPayload($id, array $payload)
    {
        return new self($id, $payload['roles']);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->id;
    }

    /**
     * @return string|void
     */
    public function getPassword()
    {
        return;
    }

    /**
     * @return null|string|void
     */
    public function getSalt()
    {
        return;
    }

    /**
     * @return null|string|void
     */
    public function eraseCredentials()
    {
        return;
    }
}
