<?php

namespace App\DTO\BasketChecked;

use Symfony\Component\Serializer\Annotation\Groups;

class BasketCheckedModel
{
    /**
     * @var BasketCheckedModelDiscount[]|null
     * @Groups({"check"})
     */
    protected $discounts;

    /**
     * @var BasketCheckedModelBonus[]|null
     * @Groups({"check"})
     */
    protected $bonuses;

    /**
     * @var int
     * @Groups({"check"})
     */
    protected $quantity;

    /**
     * @var float
     * @Groups({"check"})
     */
    protected $price;

    /**
     * @var float
     * @Groups({"check"})
     */
    protected $cost;

    /**
     * @var float
     * @Groups({"check"})
     */
    protected $bonusAmount;

    /**
     * @var float
     * @Groups({"check"})
     */
    protected $discountAmount;

    /**
     * BasketCheckedModel constructor.
     * @param BasketCheckedModelDiscount[]|null $discounts
     * @param BasketCheckedModelBonus[]|null $bonuses
     * @param int $quantity
     * @param float $price
     * @param float $cost
     * @param float $bonusAmount
     * @param float $discountAmount
     */
    public function __construct(
        ?array $discounts,
        ?array $bonuses,
        int $quantity,
        float $price,
        float $cost,
        float $bonusAmount,
        float $discountAmount
    ) {
        $this->discounts = $discounts;
        $this->bonuses = $bonuses;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->cost = $cost;
        $this->bonusAmount = $bonusAmount;
        $this->discountAmount = $discountAmount;
    }

    /**
     * @return BasketCheckedModelDiscount[]|null
     */
    public function getDiscounts(): ?array
    {
        return $this->discounts;
    }

    /**
     * @return BasketCheckedModelBonus[]|null
     */
    public function getBonuses(): ?array
    {
        return $this->bonuses;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @return float
     */
    public function getBonusAmount(): float
    {
        return $this->bonusAmount;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }
}
