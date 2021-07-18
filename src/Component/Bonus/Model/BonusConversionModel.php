<?php

namespace App\Component\Bonus\Model;

use App\Component\Bonus\BonusException;
use App\Component\UserService\Response\BonusConversionRule;

class BonusConversionModel
{
    /**
     * @var int
     */
    private $activeBonusPoints;

    /**
     * @var BonusConversionRule[]
     */
    private $conversionRules;

    /**
     * @var UsedBonusModel|null
     */
    private $usedBonusModel;

    /**
     * BonusConversionModel constructor.
     * @param BonusConversionRule[] $conversionRules
     * @param int $activeBonusAmount
     */
    public function __construct(array $conversionRules, int $activeBonusAmount)
    {
        $this->setConversionRules($conversionRules);
        $this->setActiveBonusPoints($activeBonusAmount);
    }

    /**
     * @return int
     */
    public function getActiveBonusPoints(): int
    {
        return $this->activeBonusPoints;
    }

    /**
     * @return BonusConversionRule[]
     */
    public function getConversionRules(): array
    {
        return $this->conversionRules;
    }

    /**
     * @return UsedBonusModel|null
     */
    public function getUsedBonusModel(): ?UsedBonusModel
    {
        return $this->usedBonusModel;
    }

    /**
     * @param int $activeBonusPoints
     */
    public function setActiveBonusPoints(int $activeBonusPoints): void
    {
        $this->activeBonusPoints = $activeBonusPoints;
    }

    /**
     * @param BonusConversionRule[] $conversionRules
     */
    public function setConversionRules(array $conversionRules): void
    {
        /** @var BonusConversionRule $conversionRule */
        foreach ($conversionRules as $conversionRule) {
            $this->addConversionRule($conversionRule);
        }
    }

    /**
     * @param BonusConversionRule $rule
     */
    public function addConversionRule(BonusConversionRule $rule): void
    {
        $this->conversionRules[] = $rule;
    }

    /**
     * @param int $usedBonuses
     * @param float $orderPrice
     * @throws BonusException
     */
    public function calculate(int $usedBonuses, float $orderPrice): void
    {
        if ($usedBonuses > $this->getActiveBonusPoints()) {
            throw new BonusException(sprintf('Don\'t have enough bonus points'));
        }
        $this->usedBonusModel = $this->getCombinedBonusRule($usedBonuses, $orderPrice);
    }


    /**
     * @param int $usedBonuses
     * @param float $orderPrice
     * @return BonusConversionRule[]
     */
    public function getUsedRules(int $usedBonuses, float $orderPrice): array
    {
        $usedRules = [];

        do {
            $usedRule = $this->findConversionRule($usedBonuses, $orderPrice);
            if ($usedRule) {
                $usedBonuses -= (int)$usedRule->points;
                $orderPrice -= (float)$usedRule->cash;
                $usedRules[] = $usedRule;
            }
        } while ($usedRule !== null && $orderPrice > 0);

        return $usedRules;
    }

    public function getCombinedBonusRule(int $usedBonuses, float $orderPrice): UsedBonusModel
    {
        $discountAmount = 0;
        $usedBonusPoints = 0;

        $rules = $this->getUsedRules($usedBonuses, $orderPrice);

        /** @var BonusConversionRule $rule */
        foreach ($rules as $rule) {
            $discountAmount += $rule->cash;
            $usedBonusPoints += $rule->points;
        }

        return new UsedBonusModel($usedBonusPoints, $discountAmount);
    }

    /**
     * @param int $bonusPoints
     * @param float $orderPrice
     * @return BonusConversionRule|null
     */
    private function findConversionRule(int $bonusPoints, float $orderPrice): ?BonusConversionRule
    {
        $bonusRules = $this->conversionRules;

        usort(
            $bonusRules,
            function ($a, $b) {
                /** @var BonusConversionRule $a */
                /** @var BonusConversionRule $b */
                return ($a->points < $b->points) ? -1 : 1;
            }
        );

        $bonusRules = array_values($bonusRules);

        $returnRule = null;

        /** @var BonusConversionRule $bonusRule */
        foreach ($bonusRules as $key => $bonusRule) {
            if ($bonusRule->points > $bonusPoints) {
                break;
            }

            $nextRule = $bonusRules[$key + 1] ?? null;

            $rulePrice = $orderPrice - $bonusRule->cash;

            if (!$nextRule || $nextRule->points > $bonusPoints || $rulePrice <= 0) {
                $returnRule = $bonusRule;
                break;
            }

            $nextRulePrice = $nextRule ? $orderPrice - $nextRule->cash : 0;
            if ($nextRulePrice < 0) {
                $ruleBonusAmount = $bonusPoints - $bonusRule->points;
                $combined = $this->getCombinedBonusRule($ruleBonusAmount, $rulePrice);

                if (!$combined->getBonusPoints() && $rulePrice - abs($nextRulePrice) < 0) {
                    $returnRule = $bonusRule;
                    break;
                }

                $combinedPrice = $rulePrice - $combined->getDiscountAmount();
                $combinedPoints = $bonusRule->points + $combined->getBonusPoints();
                $combinedDiffPrices = $combinedPrice - abs($nextRulePrice);

                if ($combined->getBonusPoints() && $combinedDiffPrices < 0 && $combinedPoints <= $nextRule->points) {
                    $returnRule = $bonusRule;
                    break;
                }
            }
        }

        return $returnRule;
    }
}
