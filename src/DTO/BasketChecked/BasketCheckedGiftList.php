<?php

namespace App\DTO\BasketChecked;

use Symfony\Component\Serializer\Annotation\Groups;

class BasketCheckedGiftList
{
    /**
     * @var string
     * @Groups({"check"})
     */
    protected $id;

    /**
     * @var string
     * @Groups({"check"})
     */
    protected $name;

    /**
     * @var string|null
     * @Groups({"check"})
     */
    protected $toNomenclature;

    /**
     * @var BasketCheckedGiftNomenclature[]
     * @Groups({"check"})
     */
    protected $nomenclatureList;

    /**
     * @var bool
     * @Groups({"check"})
     */
    protected $isSelectable;

    /**
     * @var string|null
     */
    protected $chosenGiftNomenclature;

    /**
     * BasketCheckedGiftList constructor.
     * @param string $id
     * @param string $name
     * @param string|null $toNomenclature
     * @param BasketCheckedGiftNomenclature[] $nomenclatureList
     * @param bool $isSelectable
     */
    public function __construct(
        string $id,
        string $name,
        ?string $toNomenclature,
        array $nomenclatureList,
        bool $isSelectable
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->toNomenclature = $toNomenclature;
        $this->nomenclatureList = $nomenclatureList;
        $this->isSelectable = $isSelectable;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getToNomenclature(): ?string
    {
        return $this->toNomenclature;
    }

    /**
     * @return BasketCheckedGiftNomenclature[]
     */
    public function getNomenclatureList(): array
    {
        return $this->nomenclatureList;
    }

    /**
     * @return bool
     */
    public function getIsSelectable(): bool
    {
        return $this->isSelectable;
    }

    /**
     * @return string|null
     */
    public function getChosenGiftNomenclature(): ?string
    {
        return $this->chosenGiftNomenclature;
    }

    public function findNomenclature(string $nomenclatureId): ?BasketCheckedGiftNomenclature
    {
        foreach ($this->nomenclatureList ?? [] as $nomenclature) {
            if ($nomenclature->getNomenclatureId() === $nomenclatureId) {
                return $nomenclature;
            }
        }

        return null;
    }

    /**
     * @param BasketCheckedGiftNomenclature[] $nomenclatureList
     */
    public function setNomenclatureList(array $nomenclatureList): void
    {
        $this->nomenclatureList = $nomenclatureList;
    }

    /**
     * @param string|null $chosenGiftNomenclature
     */
    public function setChosenGiftNomenclature(?string $chosenGiftNomenclature): void
    {
        $this->chosenGiftNomenclature = $chosenGiftNomenclature;
    }

    public function getGivenGifts(): array
    {
        if (
            $this->isSelectable &&
            $this->chosenGiftNomenclature &&
            $this->findNomenclature($this->chosenGiftNomenclature)
        ) {
            return [$this->findNomenclature($this->chosenGiftNomenclature)];
        }
        if (!$this->isSelectable) {
            return $this->nomenclatureList ?? [];
        }

        return [];
    }
}
