<?php

namespace App\Component\Payment\Fondy;

use App\Component\Payment\Fondy\Model\PaymentByTokenModel;
use App\Entity\Payment;
use App\Component\Payment\Fondy\Model\CheckoutModel;
use App\Component\Payment\Fondy\Model\PaymentModel;

class ModelBuilder
{
    /**
     * @param PaymentModel $prototype
     * @param Payment $payment
     * @return PaymentModel
     */
    public function buildPaymentModel(PaymentModel $prototype, Payment $payment): PaymentModel
    {
        $model = clone $prototype;

        $model->paymentHash = (string)$payment->getHash();
        $model->orderId = (string)$payment->getNumber();
        if ($payment->getOrder()) {
            $model->productId = (string)$payment->getOrder()->getNumber();
        }
        $desc = $payment->getDescription();

        $model->orderDesc = mb_strlen($desc) <= 1024 ? $desc : mb_substr($desc, 0, 1014) . '...' . mb_substr($desc, -7);
        $model->amount = (int)($payment->getCost() * 100);
        $model->returnCardToken = $payment->isForCardToken();
        $model->cardVerification = $payment->isTypeCardVerification();

        return $model;
    }

    /**
     * @param PaymentByTokenModel $prototype
     * @param Payment $payment
     * @return PaymentByTokenModel
     * @throws FondyException
     */
    public function buildPaymentByToken(PaymentByTokenModel $prototype, Payment $payment): PaymentByTokenModel
    {
        $model = clone $prototype;

        $model->paymentHash = (string)$payment->getHash();
        $model->orderId = (string)$payment->getNumber();
        if ($payment->getOrder()) {
            $model->productId = (string)$payment->getOrder()->getNumber();
        }
        $desc = $payment->getDescription();

        $model->orderDesc = mb_strlen($desc) <= 1024 ? $desc : mb_substr($desc, 0, 1014) . '...' . mb_substr($desc, -7);
        $model->amount = (int)($payment->getCost() * 100);

        $card = $payment->getCard();

        if (!$card || !$card->getIsVerified()) {
            throw new FondyException(printf('Card for payment %s not exist', $payment->getNumber()));
        }

        $model->rectoken = $card->getToken();

        return $model;
    }

    /**
     * @param CheckoutModel $prototype
     * @param Payment $payment
     * @return CheckoutModel
     */
    public function buildCheckoutModel(CheckoutModel $prototype, Payment $payment): CheckoutModel
    {
        $model = clone $prototype;

        $model->orderId = (string)$payment->getNumber();

        return $model;
    }
}
