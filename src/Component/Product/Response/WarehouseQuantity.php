<?php

namespace App\Component\Product\Response;

use Symfony\Component\Serializer\Annotation\Groups;

class WarehouseQuantity
{
    /**
     * @var string
     * @Groups({"check"})
     */
    public $ref;

    /**
     * @var string
     * @Groups({"check"})
     */
    public $slug;

    /**
     * @var integer
     * @Groups({"check"})
     */
    public $quantity;

    /**
     * @var bool
     * @Groups({"check"})
     */
    public $saleEnable;

    /**
     * WarehouseQuantity constructor.
     * @param string $ref
     * @param string $slug
     * @param int $quantity
     * @param bool $saleEnable
     */
    public function __construct(string $ref, string $slug, int $quantity, bool $saleEnable)
    {
        $this->ref = $ref;
        $this->slug = $slug;
        $this->quantity = $quantity;
        $this->saleEnable = $saleEnable;
    }
}
