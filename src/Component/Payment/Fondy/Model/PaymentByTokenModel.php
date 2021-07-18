<?php

namespace App\Component\Payment\Fondy\Model;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaymentByTokenModel extends AbstractModel
{
    /**
     * @var string|null
     * @Groups({"signature","request"})
     */
    public $rectoken;

    /**
     * @var string
     */
    public $paymentHash = '';
    /**
     * @var string
     * @Groups({"signature","request"})
     */
    public $orderId = '';

    /**
     * @var string
     * @Groups({"signature","request"})
     */
    public $productId = '';

    /**
     * @var string
     * @Groups({"signature","request"})
     */
    public $orderDesc;

    /**
     * @var integer
     * @Groups({"signature","request"})
     */
    public $amount = 0;

    /**
     * @var string
     * @Groups({"signature","request"})
     */
    public $currency = '';

    /**
     * @var string
     */
    private $serverCallbackUrl;

    /**
     * PaymentModel constructor.
     * @param NormalizerInterface $normalizer
     * @param string $password
     * @param string $merchantId
     * @param string $serverCallbackUrl
     * @param string $currency
     */
    public function __construct(
        NormalizerInterface $normalizer,
        string $password,
        string $merchantId,
        string $serverCallbackUrl,
        string $currency
    ) {
        parent::__construct($normalizer, $password, $merchantId);

        $this->serverCallbackUrl = $serverCallbackUrl;
        $this->currency = $currency;
    }

    /**
     * @return string
     * @Groups({"signature","request"})
     */
    public function getServerCallbackUrl(): string
    {
        return sprintf($this->serverCallbackUrl, $this->paymentHash);
    }
}
