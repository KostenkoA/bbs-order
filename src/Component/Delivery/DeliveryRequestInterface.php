<?php

namespace App\Component\Delivery;

interface DeliveryRequestInterface
{
    public const CARRIER_NOVA_POSHTA_TYPE = 'nova-poshta';

    public const CARRIER_JUSTIN_TYPE = 'justin';

    public const CARRIER_MEEST_EXPRESS = 'meest';
}
