<?php

namespace App\EventListener;

use App\Component\ESputnik\ESputnikActionException;
use App\Component\ESputnik\Response\ESputnikError;
use App\Entity\Order;
use App\Entity\OrderStatusHistory;
use App\Event\OrderErrorEvent;
use App\Interfaces\OrderEventInterface;
use App\Service\ESputnikService;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OrderStatusHistoryListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var ESputnikService
     */
    private $sputnikService;

    /**
     * OrderStatusHistoryListener constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param NormalizerInterface $normalizer
     */
    public function __construct(EntityManagerInterface $entityManager, NormalizerInterface $normalizer, ESputnikService $sputnikService)
    {
        $this->entityManager = $entityManager;
        $this->normalizer = $normalizer;
        $this->sputnikService = $sputnikService;
    }

    /**
     * @param OrderEventInterface $event
     * @throws ESputnikActionException
     * @throws ESputnikError
     * @throws GuzzleException
     */
    public function insert(OrderEventInterface $event): void
    {
        $orderStatus = new OrderStatusHistory();

        /** @var Order $order */
        if ($order = $event->getOrder()) {
            $orderStatus->setStatus($order->getStatus());
            $orderStatus->setOrder($order);

            $this->entityManager->persist($orderStatus);
            $this->entityManager->flush();
            $this->sputnikService->sendOrder($order);
        }
    }

    /**
     * @param OrderErrorEvent $event
     * @throws ESputnikActionException
     * @throws ESputnikError
     * @throws GuzzleException
     */
    public function insertError(OrderErrorEvent $event)
    {
        $orderStatus = new OrderStatusHistory();

        /** @var Order $order */
        if ($order = $event->getOrder()) {
            $orderStatus->setStatus($order->getStatus());
            $orderStatus->setOrder($order);

            $orderStatus->setData(
                $this->normalizer->normalize(
                    $event->getOrderError(),
                    'array',
                    ['groups' => ['save']]
                )
            );

            $this->entityManager->persist($orderStatus);
            $this->entityManager->flush();
            $this->sputnikService->sendOrder($order);
        }
    }
}
