<?php

namespace App\Component\Basket\DTO;

class BasketGiftListResponse
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var string|null */
    public $toNomenclature;

    /** @var BasketGiftNomenclatureResponse[] */
    public $nomenclatureList;

    /** @var bool */
    public $isSelectable;
}
