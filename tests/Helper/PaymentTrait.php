<?php

namespace App\Tests\Helper;

use App\Entity\Order;
use App\Entity\Payment;
use App\Entity\PaymentMethodInterface;

trait PaymentTrait
{
    public function createPayment(Order $order): Payment
    {
        $payment = new Payment($order, null, Payment::TYPE_ORDER_PAYMENT, PaymentMethodInterface::FONDY_METHOD, 0);

        $em = $this->getEntityManager();
        $em->persist($payment);
        $em->flush();

        return $payment;
    }
}
