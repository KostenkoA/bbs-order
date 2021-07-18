<?php

namespace App\Component\Delivery\Response;

class SettlementResponse
{
    /** @var Settlement */
    public $settlement;

    /** @var Warehouse|null */
    public $warehouse = null;

    /** @var Street|null  */
    public $street = null;
}
