<?php

namespace App\DTO;

use App\Security\User;

class OrderSearch
{
    /**
     * @var Sort[]|null
     */
    public $sort;

    /**
     * @var Pagination|null
     */
    public $pagination;

    /**
     * @var string
     */
    public $projectName;

    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserRef(): string
    {
        return $this->user ? $this->user->getId() : '';
    }
}
