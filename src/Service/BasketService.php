<?php

namespace App\Service;

use App\Builder\BasketCheckedBuilder;
use App\Component\Basket\BasketComponent;
use App\Component\Basket\BasketException;
use App\Component\Product\ProductSearchComponent;
use App\Component\Product\ProductSearchException;
use App\Component\RequestResponseException;
use App\DTO\BasketChecked\BasketChecked;
use App\DTO\Basket\Basket;
use App\DTO\BasketChecked\BasketCheckedItem;
use App\DTO\NewOrder;

class BasketService
{
    /**
     * @var ProductSearchComponent
     */
    private $productSearchComponent;

    /**
     * @var BasketComponent
     */
    private $basketComponent;

    /**
     * @var BasketCheckedBuilder
     */
    private $basketBuilder;

    public function __construct(
        ProductSearchComponent $productSearchComponent,
        BasketComponent $basketComponent,
        BasketCheckedBuilder $basketBuilder
    ) {
        $this->productSearchComponent = $productSearchComponent;
        $this->basketComponent = $basketComponent;
        $this->basketBuilder = $basketBuilder;
    }

    /**
     * @param NewOrder $dto
     * @return BasketChecked
     */
    public function checkByNewOrder(NewOrder $dto): BasketChecked
    {
        $basketDto = new Basket();
        $basketDto->project = $dto->project;
        //TODO: temporary off
//        $basketDto->bonus = $dto->usedBonuses;
        $basketDto->phone = $dto->phone;
        $basketDto->certificates = $dto->certificates;

        $basketDto->basketItems = $dto->orderItems;

        $basket = $this->checkByBasket($basketDto);

        foreach ($dto->chosenGiftItems as $gift) {
            $giftList = $basket->findGiftList($gift->giftDiscountRef);
            if ($giftList && $giftList->findNomenclature($gift->internalId)) {
                $giftList->setChosenGiftNomenclature($gift->internalId);
            }
        }

        return $basket;
    }

    /**
     * @param Basket $dto
     * @return BasketChecked
     */
    public function checkByBasket(Basket $dto): BasketChecked
    {
        $basket = new BasketChecked();

        foreach ($dto->basketItems as $basketItem) {
            $basket->addBasketItem(new BasketCheckedItem($basketItem->internalId, $basketItem->quantity));
        }
        $this->updateByProducts($dto->project, $basket);

        try {
            $basketModelResponse = $this->basketComponent->calculateFromBasket($dto, $basket);
            $this->basketBuilder->fillByBasketModelResponse($basket, $basketModelResponse);
        } catch (BasketException | RequestResponseException $e) {
        }

        $this->updateByProducts($dto->project, $basket);

        return $basket;
    }

    /**
     * @param string $project
     * @param BasketChecked $basket
     */
    private function updateByProducts(string $project, BasketChecked $basket): void
    {
        $searchNomenclatures = [];
        foreach ($basket->getBasketItems() ?? [] as $basketItem) {
            $searchNomenclatures[] = $basketItem->getInternalId();
        }

        foreach ($basket->getGiftLists() ?? [] as $giftList) {
            foreach ($giftList->getNomenclatureList() as $nomenclature) {
                $searchNomenclatures[] = $nomenclature->getNomenclatureId();
            }
        }

        $responseException = null;
        try {
            $this->productSearchComponent->searchProducts($project, $searchNomenclatures);
        } catch (RequestResponseException | ProductSearchException $e) {
            $responseException = $e;
        }

        foreach ($basket->getBasketItems() as $basketItem) {
            try {
                $product = $this->productSearchComponent->getFromContainer($project, $basketItem->getInternalId());

                $basketItem->setProduct($product);
            } catch (ProductSearchException $e) {
                $basketItem->setSearchException($responseException ?? $e);
            }
        }
        $basket->calculateAfterProducts();

        foreach ($basket->getGiftLists() ?? [] as $giftList) {
            $nomenclatureList = [];
            foreach ($giftList->getNomenclatureList() as $nomenclature) {
                try {
                    $product = $this->productSearchComponent->getFromContainer(
                        $project,
                        $nomenclature->getNomenclatureId()
                    );
                    $nomenclature->setProduct($product);
                    $nomenclatureList[] = $nomenclature;
                } catch (ProductSearchException $e) {
                }
            }
            $giftList->setNomenclatureList($nomenclatureList);
        }
    }
}
