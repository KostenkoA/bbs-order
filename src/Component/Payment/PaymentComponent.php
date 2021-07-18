<?php

namespace App\Component\Payment;

use App\Component\Payment\Method\PaymentMethodInterface;
use App\Entity\Payment;
use Symfony\Component\DependencyInjection\ServiceLocator;

class PaymentComponent
{
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    /**
     * @var array
     */
    private $availableMethods;

    /**
     * PaymentService constructor.
     * @param ServiceLocator $serviceLocator
     * @param array $availableMethods
     */
    public function __construct(ServiceLocator $serviceLocator, array $availableMethods)
    {
        $this->serviceLocator = $serviceLocator;
        $this->availableMethods = $availableMethods;
    }

    /**
     * @param Payment $payment
     * @throws PaymentException
     */
    public function checkPaymentEnable(Payment $payment): void
    {
        if ($payment->getCost() <= 0) {
            throw new PaymentException('Payment cost <= 0');
        }
    }

    /**
     * @param Payment $payment
     * @return NewPaymentInterface
     * @throws PaymentException
     */
    public function createPayment(Payment $payment): NewPaymentInterface
    {
        $paymentMethod = $this->getPaymentMethod($payment->getMethod());

        return $paymentMethod->newPayment($payment);
    }

    /**
     * @param Payment $payment
     * @return CheckoutPaymentInterface
     * @throws PaymentException
     */
    public function checkoutPayment(Payment $payment): CheckoutPaymentInterface
    {
        $paymentMethod = $this->getPaymentMethod($payment->getMethod());

        return $paymentMethod->checkPayment($payment);
    }

    /**
     * @param Payment $payment
     * @return CheckoutPaymentInterface
     * @throws PaymentException
     */
    public function paymentByToken(Payment $payment): CheckoutPaymentInterface
    {
        $paymentMethod = $this->getPaymentMethod($payment->getMethod());

        return $paymentMethod->paymentByToken($payment);
    }

    /**
     * @param int $method
     * @return PaymentMethodInterface
     * @throws PaymentException
     */
    protected function getPaymentMethod(int $method): PaymentMethodInterface
    {
        $methodClass = $this->availableMethods[$method] ?? null;
        if (!$methodClass || !$this->serviceLocator->has($methodClass)) {
            throw new PaymentException(sprintf('Acquiring method %s not found', $method));
        }

        $method = $this->serviceLocator->get($methodClass);

        if (!($method instanceof PaymentMethodInterface)) {
            throw new PaymentException(sprintf('Acquiring method %s not PaymentMethodInterface', $method));
        }

        return $method;
    }
}
