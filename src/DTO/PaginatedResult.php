<?php

namespace App\DTO;

class PaginatedResult
{
    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $count;

    /**
     * @var array
     */
    public $items;

    /**
     * Pagination constructor.
     * @param int $page
     * @param int $limit
     */
    public function __construct(?int $page = null, ?int $limit = null)
    {
        $this->page = $page ?? 1;
        $this->limit = $limit ?? 20;
    }

    /**
     * @return int
     */
    public function getFirstResult(): int
    {
        return (($this->page - 1) * $this->limit);
    }
}
