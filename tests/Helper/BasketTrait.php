<?php

namespace App\Tests\Helper;

trait BasketTrait
{
    public function checkBasketInfo(array $response): void
    {
        foreach ($response['basketItems'] as $basketItem) {
            $this->assertArrayHasKey('internalId', $basketItem);
            $this->assertArrayHasKey('quantity', $basketItem);
            $this->assertArrayHasKey('availableQuantity', $basketItem);
            $this->assertArrayHasKey('recommendedPrice', $basketItem);
            $this->assertArrayHasKey('sellingPrice', $basketItem);
        }
    }
}
