<?php

namespace App\Component\Basket\DTO;

class BasketModelResponse
{
    /** @var BasketItemResponse[] */
    public $basketItemList = [];

    /** @var BasketGiftListResponse[] */
    public $giftLists = [];

    /** @var float|int */
    public $bonusAmount;

    /** @var float|int */
    public $discountAmount;

    /** @var float|int */
    public $cost;
}
