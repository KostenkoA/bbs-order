<?php


namespace App\Tests\Model;

use App\Component\Payment\CheckoutPaymentInterface;
use App\Entity\PaymentStatusInterface;

class CheckoutPaymentTest implements CheckoutPaymentInterface
{
    /**
     * @var int
     */
    private $paymentStatus;

    public function __construct(int $paymentStatus = PaymentStatusInterface::STATUS_APPROVED)
    {
        $this->paymentStatus = $paymentStatus;
    }

    public function getPaymentStatus(): int
    {
        return $this->paymentStatus;
    }
}
