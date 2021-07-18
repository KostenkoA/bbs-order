<?php

namespace App\Component\Basket\DTO;

class BasketItemRequest
{
    /** @var string */
    public $nomenclatureId;

    /** @var int */
    public $quantity = 0;

    /** @var float|null */
    public $sellingPrice;

    /**
     * BasketItemRequest constructor.
     * @param string $nomenclatureId
     * @param int $quantity
     * @param float|null $sellingPrice
     */
    public function __construct(string $nomenclatureId, int $quantity, ?float $sellingPrice)
    {
        $this->nomenclatureId = $nomenclatureId;
        $this->quantity = $quantity;
        $this->sellingPrice = $sellingPrice;
    }
}
