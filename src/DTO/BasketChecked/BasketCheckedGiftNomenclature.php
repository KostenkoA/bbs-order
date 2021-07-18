<?php

namespace App\DTO\BasketChecked;

use App\Component\Product\Response\Product;
use Symfony\Component\Serializer\Annotation\Groups;

class BasketCheckedGiftNomenclature
{
    /**
     * @var string
     * @Groups({"check"})
     */
    protected $nomenclatureId;

    /**
     * @var int
     * @Groups({"check"})
     */
    protected $quantity;

    /**
     * @var Product|null
     */
    protected $product;

    public function __construct(string $nomenclatureId, int $quantity, ?Product $product = null)
    {
        $this->nomenclatureId = $nomenclatureId;
        $this->quantity = $quantity;
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getNomenclatureId(): string
    {
        return $this->nomenclatureId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }
}
