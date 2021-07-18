<?php

namespace App\DTO\BasketChecked;

use Symfony\Component\Serializer\Annotation\Groups;

class BasketCheckedModelDiscount
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     * @Groups({"check"})
     */
    protected $title;

    /**
     * @var float
     * @Groups({"check"})
     */
    protected $amount;

    public function __construct(string $id, string $title, float $amount)
    {
        $this->id = $id;
        $this->title = $title;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
