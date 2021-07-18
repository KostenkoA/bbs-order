<?php

namespace App\Component\Product;

use App\Component\Product\Response\Product;
use App\Component\Product\Request\ProductSearchRequest;
use App\Component\RequestResponseException;
use Psr\Container\ContainerInterface;

class ProductSearchComponent
{
    /** @var ContainerInterface */
    private $requestLocator;

    /** @var ProductBuilder */
    private $productBuilder;

    /** @var ProductNomenclatureContainer */
    private $productContainer;

    /**
     * ProductComponent constructor.
     * @param ContainerInterface $requestLocator
     * @param ProductBuilder $productBuilder
     * @param ProductNomenclatureContainer $productContainer
     */
    public function __construct(
        ContainerInterface $requestLocator,
        ProductBuilder $productBuilder,
        ProductNomenclatureContainer $productContainer
    ) {
        $this->requestLocator = $requestLocator;
        $this->productBuilder = $productBuilder;
        $this->productContainer = $productContainer;
    }

    /**
     * @param $class
     * @return ProductSearchRequestInterface
     * @throws ProductSearchException
     */
    private function getRequest($class): ProductSearchRequestInterface
    {
        if (!$this->requestLocator->has($class)) {
            throw new ProductSearchException('Can\'t found request by class ' . $class);
        }
        /** @var ProductSearchRequestInterface $action */
        $action = $this->requestLocator->get($class);

        return $action;
    }

    /**
     * @param string $project
     * @param string $nomenclature
     * @return Product
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function getByNomenclature(string $project, string $nomenclature): Product
    {
        $this->searchProducts($project, [$nomenclature]);

        $products = $this->getByNomenclatureList($project, [$nomenclature]);

        return array_shift($products);
    }

    /**
     * @param string $project
     * @param string $nomenclature
     * @return Product
     * @throws ProductSearchException
     */
    public function getFromContainer(string $project, string $nomenclature): Product
    {
        if ($product = $this->productContainer->get($project, $nomenclature)) {
            return $product;
        }

        throw new ProductSearchException(sprintf('Nomenclature %s  not found', $nomenclature), 404);
    }

    /**
     * @param string $project
     * @param array $nomenclatureList
     * @return Product[]
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function getByNomenclatureList(string $project, array $nomenclatureList): array
    {
        $this->searchProducts($project, $nomenclatureList);

        return $this->productContainer->getList($project, $nomenclatureList);
    }

    /**
     * @param string $project
     * @param array $nomenclatureList
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function searchProducts(string $project, array $nomenclatureList): void
    {
        $nomenclatureList = $this->productContainer->getExcluded($project, $nomenclatureList);

        /** @var ProductSearchRequest $request */
        $request = $this->getRequest(ProductSearchRequest::class);

        $request->setNomenclatureList($nomenclatureList);
        $request->setProject($project);
        $request->send();

        $response = $request->handleResponse();

        foreach ($response as $item) {
            foreach ($nomenclatureList as $nomenclature) {
                if ($product = $this->productBuilder->buildProduct($item, $nomenclature)) {
                    $this->productContainer->set($project, $nomenclature, $product);
                }
            }
        }
    }
}
