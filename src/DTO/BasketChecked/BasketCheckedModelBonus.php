<?php

namespace App\DTO\BasketChecked;

use Symfony\Component\Serializer\Annotation\Groups;

class BasketCheckedModelBonus
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
    protected $points;

    /**
     * @var string
     * @Groups({"check"})
     */
    protected $accrualDate;

    public function __construct(string $id, string $title, float $points, string $accrualDate)
    {
        $this->id = $id;
        $this->title = $title;
        $this->points = $points;
        $this->accrualDate = $accrualDate;
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
    public function getPoints(): float
    {
        return $this->points;
    }

    /**
     * @return string
     */
    public function getAccrualDate(): string
    {
        return $this->accrualDate;
    }
}
