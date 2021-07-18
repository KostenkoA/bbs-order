<?php

namespace App\Entity\Value;

use Symfony\Component\Serializer\Annotation\Groups;

class OrderItemDiscount
{
    /**
     * @var string
     * @Groups({"admin.info"})
     */
    protected $discountRef;

    /**
     * @var string
     * @Groups({"info","admin.info","email"})
     */
    protected $title;

    /**
     * @var float
     * @Groups({"info","admin.info","email"})
     */
    protected $amount;

    /**
     * OrderItemDiscount constructor.
     * @param string|null $discountRef
     * @param string $title
     * @param float $amount
     */
    public function __construct(string $discountRef, string $title, float $amount)
    {
        $this->discountRef = $discountRef;
        $this->title = $title;
        $this->amount = $amount;
    }

    /**
     * @return string|null
     */
    public function getDiscountRef(): string
    {
        return $this->discountRef;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}
