<?php

namespace App\Component\Delivery\Request;

use App\Component\Delivery\DeliveryRequestAbstract;
use App\Component\Delivery\DeliveryRequestInterface;

class WarehouseSearchRequest extends DeliveryRequestAbstract implements DeliveryRequestInterface
{
    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $settlementId;

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $settlementId
     */
    public function setSettlementId(string $settlementId): void
    {
        $this->settlementId = $settlementId;
    }

    protected function getRequestOptions(): array
    {
        return [];
    }

    protected function getActionUri(): string
    {
        return sprintf('/public/settlement/%s/%s/prepare-warehouse/%s', $this->settlementId, $this->type, $this->id);
    }
}
