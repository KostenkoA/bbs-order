<?php

namespace App\Component\Payment\Fondy\Model;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Annotation as Serializer;

class PaymentModel extends AbstractModel
{
    /**
     * @var string
     */
    public $paymentHash = '';
    /**
     * @var string
     * @Serializer\Groups({"signature","request"})
     */
    public $orderId = '';

    /**
     * @var string
     * @Serializer\Groups({"signature","request"})
     */
    public $productId = '';

    /**
     * @var string
     * @Serializer\Groups({"signature","request"})
     */
    public $orderDesc;

    /**
     * @var integer
     * @Serializer\Groups({"signature","request"})
     */
    public $amount = 0;

    /**
     * @var string
     * @Serializer\Groups({"signature","request"})
     */
    public $currency = '';

    /**
     * @var bool
     */
    public $returnCardToken = false;

    /**
     * @var bool
     */
    public $cardVerification = false;

    /**
     * @var string
     */
    private $publicCallbackUrl;

    /**
     * @var string
     */
    private $serverCallbackUrl;

    /**
     * PaymentModel constructor.
     * @param NormalizerInterface $normalizer
     * @param string $password
     * @param string $merchantId
     * @param string $publicCallbackUrl
     * @param string $serverCallbackUrl
     * @param string $currency
     */
    public function __construct(
        NormalizerInterface $normalizer,
        string $password,
        string $merchantId,
        string $publicCallbackUrl,
        string $serverCallbackUrl,
        string $currency
    ) {
        parent::__construct($normalizer, $password, $merchantId);

        $this->serverCallbackUrl = $serverCallbackUrl;
        $this->publicCallbackUrl = $publicCallbackUrl;
        $this->currency = $currency;
    }

    /**
     * @return string
     * @Serializer\Groups({"signature","request"})
     */
    public function getServerCallbackUrl(): string
    {
        return sprintf($this->serverCallbackUrl, $this->paymentHash);
    }

    /**
     * @return string
     * @Serializer\Groups({"signature","request"})
     */
    public function getResponseUrl(): string
    {
        return sprintf($this->publicCallbackUrl, $this->paymentHash);
    }

    /**
     * @return string
     * @Serializer\Groups({"signature","request"})
     */
    public function getRequiredRectoken(): string
    {
        return $this->returnCardToken ? 'Y' : 'N';
    }

    /**
     * @return string
     * @Serializer\Groups({"signature","request"})
     */
    public function getVerification(): string
    {
        return $this->cardVerification ? 'Y' : 'N';
    }
}
