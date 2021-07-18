<?php

namespace App\DTO\BasketChecked;

use App\Component\Product\ProductSearchException;
use App\Component\Product\Response\Product;
use App\Component\Product\Response\WarehouseQuantity;
use App\Component\RequestResponseException;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

class BasketCheckedItem
{
    /** @var string */
    protected $internalId;

    /** @var integer */
    protected $expectedQuantity;

    /** @var Product */
    protected $product;

    /** @var int|null */
    protected $errorCode;

    /** @var BasketCheckedModel|null */
    protected $checked;

    /** @var RequestResponseException | ProductSearchException */
    protected $searchException;

    public function __construct(string $internalId, int $expectedQuantity)
    {
        $this->internalId = $internalId;
        $this->expectedQuantity = $expectedQuantity;
    }

    /**
     * @return string
     * @Groups({"subscription-planning", "check"})
     */
    public function getInternalId(): string
    {
        return $this->internalId;
    }

    /**
     * @return int
     * @Groups({"subscription-planning", "check"})
     */
    public function getExpectedQuantity(): int
    {
        return $this->expectedQuantity;
    }

    /**
     * @return int
     * @Groups({"check"})
     */
    public function getQuantity(): int
    {
        return $this->expectedQuantity <= $this->getAvailableQuantity() ?
            $this->expectedQuantity : $this->getAvailableQuantity();
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @return string|null
     * @Groups({"subscription-planning", "check"})
     */
    public function getProductId(): ?string
    {
        return $this->product->productId ?? null;
    }

    /**
     * @return string|null
     * @Groups({"check"})
     */
    public function getTitle(): ?string
    {
        return $this->product->title ?? null;
    }

    /**
     * @return string|null
     * @Groups({"check"})
     */
    public function getTitleUkr(): ?string
    {
        return $this->product->titleUkr ?? null;
    }

    /**
     * @return int
     * @Groups({"check"})
     */
    public function getAvailableQuantity(): int
    {
        return $this->product->totalAmount ?? 0;
    }

    /**
     * @return float|null
     * @Groups({"check"})
     */
    public function getRecommendedPrice(): ?float
    {
        return $this->product->recommendedPrice ?? null;
    }

    /**
     * @return float|null
     * @Groups({"check"})
     */
    public function getSellingPrice(): ?float
    {
        return $this->product->sellingPrice ?? null;
    }

    /**
     * @return WarehouseQuantity[]|null
     * @Groups({"check"})
     */
    public function getWarehouseList(): ?array
    {
        return $this->product->warehouseList ?? null;
    }

    /**
     * @return int|null
     * @Groups({"check"})
     */
    public function getErrorCode(): ?int
    {
        return $this->searchException ? $this->searchException->getCode() : null;
    }

    /**
     * @return string|null
     * @Groups({"check"})
     */
    public function getErrorMessage(): ?string
    {
        return $this->searchException ? $this->searchException->getMessage() : null;
    }

    /**
     * @return RequestResponseException|ProductSearchException|null
     */
    public function getSearchException(): ?Exception
    {
        return $this->searchException;
    }

    /**
     * @return BasketCheckedModel|null
     * @Groups({"check"})
     */
    public function getChecked(): ?BasketCheckedModel
    {
        return $this->checked;
    }

    public function setCheckedModel(?BasketCheckedModel $checkedModel): void
    {
        $this->checked = $checkedModel;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    public function setSearchException(Exception $exception): void
    {
        $this->searchException = $exception;
    }
}
