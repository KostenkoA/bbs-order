<?php


namespace App\Tests\Helper;

use App\Component\Product\Response\Product;
use App\Service\ProductService;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

trait ProductTrait
{
    public function mockProductService(): void
    {
        $productData = [
            'intervalId' => 'f8e742b5-b8bb-11e7-8137-0050568e2bbe',
            'slug' => 'avtokreslo-cosmos-gruppa-01-special-edition',
            'title' => 'Автокресло Cosmos, группа 0+/1 Special Edition',
            'titleUkr' => 'Автокресло Cosmos, группа 0+/1 Special Edition',
            'shortDescription' => null,
            'shortDescriptionUkr' => null,
            'colorPresentation' => [
                'slug' => 'chernyi',
                'title' => 'Черный',
                'titleUkr' => 'Чорний',
                'ordering' => 20,
            ],
            'sizePresentation' => [],
            'heightPresentation' => null,
            'lengthPresentation' => null,
            'ageCategory' => [
                'slug' => 'ot-0-mes',
                'title' => 'от 0 мес',
                'titleUkr' => 'від 0 міс',
                'ordering' => 1,
                'gender' => 0,
            ],
            'images' => [],
            'priceList' => [
                'recommendedPrice' => [0 => ['date' => '2018-03-12', 'price' => 3990,],],
                'sellingPrices' => [0 => ['date' => '2018-12-17', 'price' => 3390,],],
            ],
            'price' => 3390,
            'sellingPrice' => 3390.0,
        ];

        $product = $this->getContainer()
            ->get(DenormalizerInterface::class)
            ->denormalize(
                $productData,
                Product::class
            );

        $service = $this->getMockBuilder(ProductService::class)->disableOriginalConstructor()->getMock();
        $service->method('getByNomenclature')->willReturn($product);

        $this->getContainer()->set(sprintf('test.%s', ProductService::class), $service);
    }
}
