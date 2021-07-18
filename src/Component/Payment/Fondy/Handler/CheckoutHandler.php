<?php

namespace App\Component\Payment\Fondy\Handler;

use App\Component\Payment\Fondy\Response\CheckoutResponse;
use App\Component\Payment\Model\Card;
use DateTime;
use Exception;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class CheckoutHandler extends HandlerAbstract
{
    protected const PAYMENT_SYSTEM_CARD = 'card';

    /**
     * @return CheckoutResponse
     * @throws Exception
     */
    protected function buildResponseDTO(): CheckoutResponse
    {
        $data = array_filter(
            $this->jsonDecode()['response'] ?? [],
            static function ($val) {
                return $val !== '';
            }
        );
        /** @var CheckoutResponse $model */
        $model = $this->denormalizer->denormalize(
            $data,
            CheckoutResponse::class,
            null,
            [
                AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
            ]
        );

        if ($data['payment_system'] ?? '' === self::PAYMENT_SYSTEM_CARD) {
            $isVerified = null;
            if (!empty($data['verification_status'])) {
                $isVerified = $this->isCardVerifiedByVerificationStatus($data['verification_status']);
            } elseif (!empty($data['order_status'])) {
                $isVerified = $this->isCardVerifiedByPaymentStatus($data['order_status']);
            }

            $model->card = new Card(
                $isVerified,
                $data['masked_card'] ?? '',
                $data['rectoken'] ?? '',
                $data['card_type'] ?? '',
                new DateTime($data['rectoken_lifetime'] ?? '')
            );
        }

        return $model;
    }

    private function isCardVerifiedByPaymentStatus(string $paymentStatus): ?bool
    {
        switch ($paymentStatus) {
            case CheckoutResponse::ORDER_STATUS_EXPIRED:
            case CheckoutResponse::ORDER_STATUS_DECLINED:
                return false;
            case CheckoutResponse::ORDER_STATUS_REVERSED:
            case CheckoutResponse::ORDER_STATUS_APPROVED:
                return true;
            case CheckoutResponse::ORDER_STATUS_PROCESSING:
            default:
                return null;
        }
    }

    private function isCardVerifiedByVerificationStatus(string $verificationStatus): ?bool
    {
        switch ($verificationStatus) {
            case CheckoutResponse::VERIFICATION_STATUS_INCORRECT:
            case CheckoutResponse::VERIFICATION_STATUS_FAILED:
                return false;
            case CheckoutResponse::VERIFICATION_STATUS_VERIFIED:
                return true;
            case CheckoutResponse::VERIFICATION_STATUS_CREATED:
            default:
                return null;
        }
    }
}
