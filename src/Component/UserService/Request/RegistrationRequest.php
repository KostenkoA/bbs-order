<?php

namespace App\Component\UserService\Request;

use App\Component\UserService\UserServiceRequestInterface;

class RegistrationRequest extends RequestAbstract implements UserServiceRequestInterface
{
    /**
     * @var array
     */
    protected $userData;

    /**
     * @param array $userData
     * @return $this
     */
    public function setUserData(array $userData): self
    {
        $this->userData = $userData;

        return $this;
    }

    /**
     * @return string[]
     */
    protected function getRequestOptions(): array
    {
        return [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => $this->userData,
        ];
    }

    /**
     * @return string
     */
    protected function getActionUri(): string
    {
        return '/public/register/phone';
    }
}
