<?php

namespace App\Builder;

use App\Component\Basket\DTO\BasketModelResponse;
use App\DTO\BasketChecked\BasketChecked;
use App\DTO\BasketChecked\BasketCheckedGiftNomenclature;
use App\DTO\BasketChecked\BasketCheckedGiftList;
use App\DTO\BasketChecked\BasketCheckedModelBonus;
use App\DTO\BasketChecked\BasketCheckedModel;
use App\DTO\BasketChecked\BasketCheckedModelDiscount;

class BasketCheckedBuilder
{
    /**
     * @param BasketChecked $basket
     * @param BasketModelResponse $basketResponse
     * @return BasketChecked
     */
    public function fillByBasketModelResponse(BasketChecked $basket, BasketModelResponse $basketResponse): BasketChecked
    {
        foreach ($basketResponse->basketItemList as $itemResponse) {
            $bonuses = [];
            foreach ($itemResponse->bonuses ?? [] as $bonusResponse) {
                $bonuses[] = new BasketCheckedModelBonus(
                    (string)$bonusResponse->id,
                    (string)$bonusResponse->title,
                    (float)$bonusResponse->points,
                    (string)$bonusResponse->accrualDate
                );
            }

            $discounts = [];
            foreach ($itemResponse->discounts ?? [] as $discountResponse) {
                $discounts[] = new BasketCheckedModelDiscount(
                    (string)$discountResponse->id,
                    (string)$discountResponse->title,
                    (float)$discountResponse->amount
                );
            }

            $basketItemChecked = new BasketCheckedModel(
                $discounts ?: null,
                $bonuses ?: null,
                (int)$itemResponse->quantity,
                (float)$itemResponse->price,
                (float)$itemResponse->cost,
                (float)$itemResponse->bonusAmount,
                (float)$itemResponse->discountAmount
            );

            $basket->setBasketItemChecked($itemResponse->nomenclatureId, $basketItemChecked);
        }

        $giftLists = [];
        foreach ($basketResponse->giftLists as $giftListResponse) {
            $nomenclatureList = [];
            foreach ($giftListResponse->nomenclatureList as $giftNomenclature) {
                $nomenclatureList[] = new BasketCheckedGiftNomenclature(
                    $giftNomenclature->nomenclatureId,
                    $giftNomenclature->quantity
                );
            }

            $giftLists[] = new BasketCheckedGiftList(
                $giftListResponse->id,
                $giftListResponse->name,
                $giftListResponse->toNomenclature,
                $nomenclatureList,
                $giftListResponse->isSelectable
            );
        }
        $basket->setGiftLists($giftLists ?: null);

        $basket->setAfterChecked(
            (float)$basketResponse->bonusAmount,
            (float)$basketResponse->discountAmount,
            (float)$basketResponse->cost
        );

        return $basket;
    }
}
