<?php

namespace App\Builder;

use App\DTO\NewOrder;
use App\DTO\User;

class UserBuilder
{
    /**
     * @param NewOrder $order
     * @return User
     */
    public function buildUserByNewOrder(NewOrder $order): User
    {
        $user = new User();

        $user->firstName = $order->firstName;
        $user->lastName = $order->lastName;
        $user->middleName = $order->middleName;
        $user->phone = $order->phone;
        $user->email = $order->email;
        $user->language = $order->userLanguageId;
        $user->project = $order->project;

        return $user;
    }
}
