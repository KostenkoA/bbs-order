<?php

namespace App\Component\Bonus;

use App\Component\Bonus\Model\BonusConversionModel;
use App\Component\Bonus\Model\UsedBonusModel;
use App\Component\UserService\UserServiceComponent;
use App\Component\UserService\UserServiceException;
use App\Component\UserService\UserServiceResponseException;
use App\Security\User;

class BonusComponent
{
    /**
     * @var UserServiceComponent
     */
    private $userComponent;

    /**
     * BonusComponent constructor.
     * @param UserServiceComponent $userComponent
     */
    public function __construct(UserServiceComponent $userComponent)
    {
        $this->userComponent = $userComponent;
    }

    /**
     * @param User $user
     * @param int $usedBonuses
     * @param float $orderPrice
     * @return UsedBonusModel|null
     * @throws BonusException
     * @throws UserServiceException
     * @throws UserServiceResponseException
     */
    public function calculateBonusExist(User $user, int $usedBonuses, float $orderPrice): ?UsedBonusModel
    {
        $profile = $this->userComponent->getBonusProfile($user);

        $bonusModel = new BonusConversionModel($profile->conversionRules, $profile->activePoints);
        $bonusModel->calculate($usedBonuses, $orderPrice);

        return $bonusModel->getUsedBonusModel();
    }
}
