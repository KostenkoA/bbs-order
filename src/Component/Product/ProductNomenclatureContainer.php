<?php

namespace App\Component\Product;

use App\Component\Product\Response\Product;

class ProductNomenclatureContainer
{
    /** @var Product[] */
    protected $productNomenclatureList = [];

    public function get(string $project, string $nomenclatureId): ?Product
    {
        return $this->productNomenclatureList[$project][$nomenclatureId] ?? null;
    }

    public function set(string $project, string $nomenclatureId, Product $product): void
    {
        $this->productNomenclatureList[$project][$nomenclatureId] = $product;
    }

    public function has(string $project, string $nomenclatureId): bool
    {
        return !empty($this->productNomenclatureList[$project][$nomenclatureId]);
    }

    /**
     * @param string $project
     * @param string[] $nomenclatureList
     * @return Product[]
     */
    public function getList(string $project, array $nomenclatureList): array
    {
        $result = [];
        foreach ($nomenclatureList as $nomenclature) {
            if ($product = $this->get($project, $nomenclature)) {
                $result[] = $product;
            }
        }

        return $result;
    }

    /**
     * @param string $project
     * @param string[] $nomenclatureList
     * @return string[]
     */
    public function getExcluded(string $project, array $nomenclatureList): array
    {
        $result = [];

        foreach ($nomenclatureList as $nomenclature) {
            if (!$this->has($project, $nomenclature)) {
                $result[] = $nomenclature;
            }
        }

        return $result;
    }
}
