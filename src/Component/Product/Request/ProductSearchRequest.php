<?php

namespace App\Component\Product\Request;

use App\Component\Product\ProductSearchRequestInterface;
use App\Component\Product\ProductSearchException;
use App\Component\RequestAbstract;
use App\Component\RequestResponseHandlerTrait;
use App\Security\User;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ProductSearchRequest
 * @method array handleResponse()
 * @package App\Component\Product\Request
 */
class ProductSearchRequest extends RequestAbstract implements ProductSearchRequestInterface
{
    use RequestResponseHandlerTrait;

    protected const REQUEST_METHOD = 'GET';

    /**
     * @var string[]
     */
    protected $nomenclatureList;

    /**
     * @var string
     */
    private $project;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * ProductSearchRequest constructor.
     * @param string $dsn
     * @param JWTEncoderInterface $jwtEncoder
     * @param SerializerInterface $serializer
     */
    public function __construct(string $dsn, JWTEncoderInterface $jwtEncoder, SerializerInterface $serializer)
    {
        parent::__construct($dsn);

        $this->serializer = $serializer;
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * @param string[] $nomenclatureList
     */
    public function setNomenclatureList(array $nomenclatureList): void
    {
        $this->nomenclatureList = $nomenclatureList;
    }

    public function setProject(string $project): void
    {
        $this->project = $project;
    }

    /**
     * @return string[]
     */
    protected function getRequestOptions(): array
    {
        $requestOptions = [
            RequestOptions::QUERY => [
                'ids' => json_decode(
                    json_encode($this->nomenclatureList),
                    true
                ),
            ],
        ];

        $headers = ['Project' => $this->project];
        if ($jwt = $this->generateJwt()) {
            $headers = array_merge(
                $headers,
                ['Authorization' => sprintf('Bearer %s', $jwt)]
            );
        }

        $requestOptions[RequestOptions::HEADERS] = $headers;

        return $requestOptions;
    }

    /**
     * @return string
     */
    protected function getActionUri(): string
    {
        return '/service/products/nomenclature';
    }

    /**
     * @return string
     */
    protected function generateJwt(): string
    {
        try {
            return $this->jwtEncoder->encode(['id' => '', 'roles' => [User::ROLE_SERVICE]]);
        } catch (JWTEncodeFailureException $e) {
            return '';
        }
    }

    /**
     * @return void
     * @throws ProductSearchException
     */
    public function send(): void
    {
        try {
            parent::send();
        } catch (ConnectException $exception) {
            throw new ProductSearchException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        } catch (RequestException $exception) {
            $this->response = $exception->getResponse();
        } catch (GuzzleException | Exception $exception) {
            throw new ProductSearchException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }

    /**
     * @param $responseContent
     * @return array
     */
    public function handleSuccess($responseContent): array
    {
        return json_decode($responseContent, true);
    }
}
