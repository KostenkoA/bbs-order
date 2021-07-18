<?php

namespace App\Entity\Value;

use Symfony\Component\Serializer\Annotation\Groups;

class OrderItemGiftDiscount
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
     * @var string|null
     * @Groups({"info","admin.info","email"})
     */
    protected $toNomenclature;

    /**
     * OrderItemGiftDiscount constructor.
     * @param string $discountRef
     * @param string $title
     * @param string|null $toNomenclature
     */
    public function __construct(string $discountRef, string $title, ?string $toNomenclature)
    {
        $this->discountRef = $discountRef;
        $this->title = $title;
        $this->toNomenclature = $toNomenclature;
    }

    /**
     * @return string
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
     * @return string|null
     */
    public function getToNomenclature(): ?string
    {
        return $this->toNomenclature;
    }
}
