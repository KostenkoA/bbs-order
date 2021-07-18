<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ResponseNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * ESDocumentNormalizer constructor.
     * @param NormalizerInterface $normalizer
     */
    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return $this->normalizer->normalize($object, $format, $context);
    }

    /**
     * @param mixed $data
     * @param null $format
     * @return bool|void
     */
    public function supportsNormalization($data, $format = null)
    {
        $this->normalizer->supportsNormalization($data, $format);
    }
}
