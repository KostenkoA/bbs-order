<?php

namespace App\EventListener;

use App\Component\ESputnik\ESputnikActionException;
use App\Component\ESputnik\Response\ESputnikError;
use App\Interfaces\OrderEventInterface;
use App\Service\ESputnikService;
use GuzzleHttp\Exception\GuzzleException;

class OrderESputnikListener
{
    /**
     * @var ESputnikService
     */
    protected $sputnikService;

    /**
     * OrderESputnikListener constructor.
     * @param ESputnikService $sputnikService
     */
    public function __construct(ESputnikService $sputnikService)
    {
        $this->sputnikService = $sputnikService;
    }

    /**
     * @param OrderEventInterface $event
     * @throws ESputnikActionException
     * @throws ESputnikError
     * @throws GuzzleException
     */
    public function sendNewOrder(OrderEventInterface $event): void
    {
        if ($order = $event->getOrder()) {
            $this->sputnikService->sendOrder($order);
        }
    }
}
