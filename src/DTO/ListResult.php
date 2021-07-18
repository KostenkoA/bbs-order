<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class ListResult
{
    /**
     * @var array[]
     * @Groups({"list"})
     */
    private $items;

    /**
     * @var integer
     * @Groups({"list"})
     */
    private $total;

    /**
     * ListResult constructor.
     * @param array|null $items
     * @param int|null $total
     */
    public function __construct(?array $items = null, ?int $total = null)
    {
        $this->items = $items ?? [];
        $this->total = $total ?? 0;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }
}
