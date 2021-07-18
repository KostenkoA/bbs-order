<?php

namespace App\DTO;

use App\Entity\Order;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Annotation as Serializer;

class OrderSearchResult
{
    /**
     * @var Order[]
     * @Serializer\Groups({"admin.list","public.list"})
     */
    public $data = [];

    /**
     * @var int
     * @Serializer\Groups({"admin.list","public.list"})
     */
    public $totalCount = 0;

    /**
     * @var int
     * @Serializer\Groups({"admin.list","public.list"})
     */
    public $offset = 0;

    /**
     * OrderSearchResult constructor.
     * @param array|null $data
     * @param int|null $totalCount
     * @param int|null $offset
     */
    public function __construct(?array $data = null, ?int $totalCount = null, ?int $offset = null)
    {
        $this->data = $data ?? [];
        $this->totalCount = $totalCount ?? 0;
        $this->offset = $offset ?? 0;
    }
}
