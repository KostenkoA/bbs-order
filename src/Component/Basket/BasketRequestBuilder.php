<?php

namespace App\Component\Basket;

use App\Component\Basket\DTO\BasketItemRequest;
use App\Component\Basket\DTO\BasketRequest;
use App\DTO\Basket\Basket;
use App\DTO\BasketChecked\BasketChecked;

class BasketRequestBuilder
{
    public function buildFromBasket(Basket $basket, BasketChecked $basketChecked): BasketRequest
    {
        $request = new BasketRequest();

        $request->phone = $basket->phone;
//        TODO: without paymentByBonuses
//        $request->paymentByBonuses = $basket->bonus;
        $request->certificates = $basket->certificates;
        foreach ($basketChecked->getBasketItems() ?? [] as $item) {
            $request->basketItems[] = new BasketItemRequest(
                $item->getInternalId(),
                $item->getQuantity(),
                $item->getSellingPrice()
            );
        }

        return $request;
    }
}
