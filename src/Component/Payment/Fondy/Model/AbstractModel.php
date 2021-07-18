<?php

namespace App\Component\Payment\Fondy\Model;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Annotation as Serializer;

abstract class AbstractModel
{
    /**
     * @var integer
     * @Serializer\Groups({"signature","request"})
     */
    protected $merchantId = 0;

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * AbstractModel constructor.
     * @param NormalizerInterface $normalizer
     * @param string $password
     * @param string $merchantId
     */
    public function __construct(NormalizerInterface $normalizer, string $password, string $merchantId)
    {
        $this->normalizer = $normalizer;
        $this->password = $password;
        $this->merchantId = $merchantId;
    }

    /**
     * @param string|null $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     * @return array|bool|float|int|string
     */
    public function normalize(string $format = null, array $context = [])
    {
        return $this->normalizer->normalize($this, $format, $context);
    }

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->merchantId;
    }

    /**
     * @return string
     * @Serializer\Groups({"request"})
     */
    public function getSignature(): string
    {
        $params = $this->normalize(
            'array',
            [
                'groups' => ['signature'],
                'skip_null_values' => true,
            ]
        );

        $params = array_filter($params, 'strlen');
        ksort($params);
        $params = array_values($params);
        array_unshift($params, $this->password);
        $params = implode('|', $params);

        return (sha1($params));
    }
}
