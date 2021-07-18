<?php

namespace App\Component\Product\Response;

class Product
{
    /** @var string */
    public $productId;

    /** @var string */
    public $intervalId;

    /** @var string */
    public $displayArticle;

    /** @var string */
    public $slug;

    /** @var string */
    public $title;

    /** @var string */
    public $titleUkr;

    /** @var array */
    public $category;

    /** @var array */
    public $folderCategory;

    /** @var array */
    public $brand;

    /** @var array */
    public $colorPresentation;

    /** @var array */
    public $sizePresentation;

    /** @var array */
    public $ageCategory;

    /** @var array */
    public $images;

    /** @var array */
    public $priceList;

    /** @var float|null */
    public $recommendedPrice;

    /** @var float */
    public $sellingPrice;

    /** @var integer */
    public $totalAmount;

    /** @var WarehouseQuantity[] */
    public $warehouseList = [];
}
