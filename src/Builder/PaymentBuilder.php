<?php


namespace App\Builder;

use App\Entity\Card;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Payment;
use App\Entity\PaymentMethodInterface;

class PaymentBuilder
{
    /**
     * @param Order $order
     * @param int $method
     * @return Payment
     */
    public function buildForOrderNewPayment(Order $order, $method = PaymentMethodInterface::FONDY_METHOD): Payment
    {
        $payment = new Payment($order, null, Payment::TYPE_ORDER_PAYMENT, $method, $order->getCost());

        $descriptions = [];
        $order->getOrderItems()->map(
            function (OrderItem $item) use (&$descriptions) {
                $descriptions[] = sprintf('%s (%s)', $item->getTitle(), $item->getQuantity());
            }
        );
        $payment->setDescription(implode('; ', $descriptions));

        return $payment;
    }

    public function buildForOrderByToken(Order $order, Card $card): Payment
    {
        $payment = new Payment($order, $card, Payment::TYPE_ORDER_PAYMENT, $card->getMethod(), $order->getCost());

        $descriptions = ['Subscription'];
        $order->getOrderItems()->map(
            function (OrderItem $item) use (&$descriptions) {
                $descriptions[] = sprintf('%s (%s)', $item->getTitle(), $item->getQuantity());
            }
        );
        $payment->setDescription(implode('; ', $descriptions));

        return $payment;
    }

    public function buildForCardVerification(Card $card): Payment
    {
        $payment = new Payment(
            null,
            $card,
            Payment::TYPE_CARD_VERIFICATION,
            $card->getMethod(),
            Payment::CARD_VERIFICATION_COST
        );
        $payment->setDescription('card verification');

        return $payment;
    }
}
