<?php

namespace App\EventListener;

use App\Component\Payment\PaymentException;
use App\Entity\Card;
use App\Entity\Order;
use App\Event\OrderPaymentByCardEvent;
use App\Exception\ObjectNotFoundException;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;

class OrderPaymentByCardListener
{
    /** @var PaymentService */
    private $paymentService;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * OrderPaymentByCard constructor.
     * @param PaymentService $paymentService
     * @param EntityManagerInterface $em
     */
    public function __construct(PaymentService $paymentService, EntityManagerInterface $em)
    {
        $this->paymentService = $paymentService;
        $this->em = $em;
    }

    /**
     * @param OrderPaymentByCardEvent $event
     * @throws PaymentException
     * @throws ObjectNotFoundException
     */
    public function onPaymentByCard(OrderPaymentByCardEvent $event): void
    {
        /** @var Order $order */
        $order = $this->em->find(Order::class, $event->getOrder()->getId());
        /** @var Card $card */
        $card = $this->em->find(Card::class, $event->getCard()->getId());

        $this->paymentService->paymentOrderByCard($order, $card);
    }
}
