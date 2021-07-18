<?php

namespace App\Component\Product;

use App\Component\Product\Response\Product;
use App\Component\Product\Response\WarehouseQuantity;
use DateTime;

class ProductBuilder
{
    private const WITHOUT_COLOR_SLUG = 'without-color';

    /**
     * @param array $data
     * @param string $nomenclature
     * @return Product|null
     */
    public function buildProduct(array $data, string $nomenclature): ?Product
    {
        try {
            $nomenclatureData = $this->getNomenclatureData($data, $nomenclature);
        } catch (ProductSearchException $e) {
            return null;
        }

        $product = new Product();
        $product->productId = $data['id'] ?? null;
        $product->slug = $data['slug'] ?? null;
        $product->title = $data['title'] ?? null;
        $product->titleUkr = $data['titleUkr'] ?? null;
        $product->folderCategory = $data['folderCategory'] ?? [];
        $product->category = $data['category'] ?? [];
        $product->brand = $data['brand'] ?? [];

        $product->intervalId = $nomenclatureData['id'] ?? null;
        $product->displayArticle = $nomenclatureData['displayArticle'] ?? null;

        $product->sizePresentation = array_merge(
            $nomenclatureData['sizePresentation'] ?? [],
            !empty($nomenclatureData['size']) ? ['id' => $nomenclatureData['size']] : []
        );

        $colorPresentation = $nomenclatureData['additionalColorPresentation'] ?? $nomenclatureData['colorPresentation'] ?? [];

        $product->images = $this->getImages($data['images'] ?? [], $nomenclatureData['colorCode'] ?? '');

        $product->colorPresentation = array_merge(
            $colorPresentation,
            !empty($nomenclatureData['color']) ? ['id' => $nomenclatureData['color']] : []
        );

        $product->ageCategory = $nomenclatureData['ageCategory'] ?? [];
        $product->priceList = $nomenclatureData['priceList'] ?? [];

        $product->sellingPrice = (float)$this->getCurrentPrice($product);
        $product->recommendedPrice = $this->getRecommendedPrice($product) ?
            (float)$this->getRecommendedPrice($product) : null;

        $product->totalAmount = (int)($nomenclatureData['totalAmount'] ?? 0);
        foreach ($nomenclatureData['warehouses'] ?? [] as $warehouseData) {
            $product->warehouseList[] = new WarehouseQuantity(
                (string)($warehouseData['ref'] ?? ''),
                (string)($warehouseData['slug'] ?? ''),
                (int)($warehouseData['quantity'] ?? 0),
                (bool)($warehouseData['saleEnable'] ?? false)
            );
        }

        return $product;
    }

    /**
     * @param array $data
     * @param string $nomenclature
     * @return array
     * @throws ProductSearchException
     */
    private function getNomenclatureData(array $data, string $nomenclature): array
    {
        if (!empty($data['nomenclature'])) {
            foreach ($data['nomenclature'] as $value) {
                if (isset($value['id']) && $value['id'] === $nomenclature) {
                    return $value;
                }
            }
        }

        throw new ProductSearchException(sprintf('Nomenclature %s  not found', $nomenclature), 404);
    }

    /**
     * @param Product $product
     * @return float|null
     */
    private function getCurrentPrice(Product $product): ?float
    {
        $price = null;
        if (!empty($product->priceList['sellingPrices']) && is_array($product->priceList['sellingPrices'])) {
            $currentPrice = $this->getAvailablePrice($product->priceList['sellingPrices']);
            $price = $currentPrice['price'] ?? null;
        }

        return $price ? (float)$price : null;
    }

    /**
     * @param Product $product
     * @return float|null
     */
    private function getRecommendedPrice(Product $product): ?float
    {
        $price = null;
        if (!empty($product->priceList['recommendedPrice']) && is_array($product->priceList['recommendedPrice'])) {
            $currentPrice = $this->getAvailablePrice($product->priceList['recommendedPrice']);
            $price = $currentPrice['price'] ?? null;
        }

        return $price ? (float)$price : null;
    }

    /**
     * @param array $priceList
     * @return array|null
     */
    private function getAvailablePrice(array $priceList): ?array
    {
        $priceList = array_filter(
            $priceList,
            static function ($price) {
                return !empty($price['date']) &&
                    DateTime::createFromFormat('Y-m-d', $price['date']) <= new DateTime();
            }
        );

        usort(
            $priceList,
            static function ($a, $b) {
                $dateA = DateTime::createFromFormat('Y-m-d', $a['date']);
                $dateB = DateTime::createFromFormat('Y-m-d', $b['date']);

                if ($dateA == $dateB) {
                    return 0;
                }

                return ($dateA < $dateB) ? 1 : -1;
            }
        );

        return !empty($priceList) ? array_shift($priceList) : null;
    }

    /**
     * @param array $images
     * @param string $colorCode
     * @return array
     */
    public function getImages(array $images, string $colorCode): array
    {
        if (!$colorCode) {
            $colorCode = self::WITHOUT_COLOR_SLUG;
        }

        $result = [];
        foreach ($images as $image) {
            if (in_array($colorCode, $image['color'] ? (array)$image['color'] : [], true)) {
                $result[] = [
                    'link' => $image['link'] ?? '',
                    'hash' => $image['hash'] ?? '',
                    'title' => $image['title'] ?? '',
                    'metaImg' => $image['metaImg'] ?? false,
                ];
            }
        }

        return $result;
    }
}
