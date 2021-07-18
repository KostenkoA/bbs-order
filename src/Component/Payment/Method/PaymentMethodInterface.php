<?php

namespace App\Component\Payment\Method;

use App\Entity\Payment;
use App\Component\Payment\CheckoutPaymentInterface;
use App\Component\Payment\NewPaymentInterface;

interface PaymentMethodInterface
{
    /**
     * @param Payment $paymentEntity
     * @return NewPaymentInterface
     */
    public function newPayment(Payment $paymentEntity): NewPaymentInterface;

    /**
     * @param Payment $paymentEntity
     * @return CheckoutPaymentInterface
     */
    public function checkPayment(Payment $paymentEntity): CheckoutPaymentInterface;

    /**
     * @param Payment $paymentEntity
     * @return CheckoutPaymentInterface
     */
    public function paymentByToken(Payment $paymentEntity): CheckoutPaymentInterface;
}
