<?php

namespace App\DTO\BasketChecked;

use Symfony\Component\Serializer\Annotation\Groups;

class BasketChecked
{
    /**
     * @var BasketCheckedItem[]|null
     * @Groups({"check"})
     */
    protected $basketItems;

    /**
     * @var BasketCheckedGiftList[]|null
     * @Groups({"check"})
     */
    protected $giftLists;

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
     * @var float
     * @Groups({"check"})
     */
    protected $cost;

    /**
     * @return BasketCheckedItem[]|null
     */
    public function getBasketItems(): ?array
    {
        return $this->basketItems;
    }

    /**
     * @return BasketCheckedGiftList[]|null
     */
    public function getGiftLists(): ?array
    {
        return $this->giftLists;
    }

    /**
     * @return float
     */
    public function getBonusAmount(): float
    {
        return $this->bonusAmount ?? 0.0;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->discountAmount ?? 0.0;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost ?? 0.0;
    }

    public function findGiftList(string $giftId): ?BasketCheckedGiftList
    {
        foreach ($this->giftLists ?? [] as $giftList) {
            if ($giftList->getId() === $giftId) {
                return $giftList;
            }
        }

        return null;
    }

    public function findBasketItem(string $internalId): ?BasketCheckedItem
    {
        foreach ($this->basketItems ?? [] as $basketItem) {
            if ($basketItem->getInternalId() === $internalId) {
                return $basketItem;
            }
        }

        return null;
    }

    /**
     * @param array|null $basketItems
     */
    public function setBasketItems(?array $basketItems): void
    {
        $this->basketItems = null;
        if (!empty($basketItems)) {
            foreach ($basketItems as $basketItem) {
                $this->addBasketItem($basketItem);
            }
        }
    }

    public function addBasketItem(BasketCheckedItem $basketCheckedItem): void
    {
        $this->basketItems[] = $basketCheckedItem;
    }

    public function setBasketItemChecked(string $itemInternalId, ?BasketCheckedModel $itemCheckedModel): void
    {
        foreach ($this->basketItems as $item) {
            if ($item->getInternalId() === $itemInternalId) {
                $item->setCheckedModel($itemCheckedModel);
            }
        }
    }

    public function setAfterChecked(float $bonusAmount, float $discountAmount, float $cost): void
    {
        $this->bonusAmount = $bonusAmount;
        $this->discountAmount = $discountAmount;
        $this->cost = $cost;
    }

    /**
     * @param BasketCheckedGiftList[]|null $giftLists
     */
    public function setGiftLists(?array $giftLists): void
    {
        $this->giftLists = null;
        if (!empty($giftLists)) {
            foreach ($giftLists as $giftList) {
                $this->addGiftList($giftList);
            }
        }
    }

    public function addGiftList(BasketCheckedGiftList $giftList): void
    {
        $this->giftLists[] = $giftList;
    }

    public function calculateAfterProducts(): void
    {
        if ($this->cost === null) {
            $cost = 0;

            foreach ($this->basketItems ?? [] as $basketItem) {
                $cost += ((float)$basketItem->getSellingPrice() * $basketItem->getQuantity());
            }

            $this->cost = $cost;
        }
    }
}
