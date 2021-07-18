<?php

namespace App\Component\Basket;

use App\Component\Basket\DTO\BasketModelResponse;
use App\Component\Basket\DTO\BasketRequest;
use App\Component\RequestAbstract;
use App\Component\RequestResponseHandlerTrait;
use App\Security\User;
use App\Interfaces\HttpRequestInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Exception;
use GuzzleHttp\RequestOptions;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\Serializer\SerializerInterface;

class CalculateBasketRequest extends RequestAbstract
{
    use RequestResponseHandlerTrait;

    /**
     * @var BasketRequest
     */
    private $basketModel;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    public function __construct(string $dsn, SerializerInterface $serializer, JWTEncoderInterface $jwtEncoder)
    {
        parent::__construct($dsn);

        $this->jwtEncoder = $jwtEncoder;
        $this->serializer = $serializer;
    }

    public function setBasketModel(BasketRequest $requestModel): void
    {
        $this->basketModel = $requestModel;
    }

    public function getBasketModel(): BasketRequest
    {
        return $this->basketModel;
    }

    protected function getRequestOptions(): array
    {
        $requestOptions = [
            RequestOptions::FORM_PARAMS => json_decode(json_encode($this->basketModel), true),
            RequestOptions::TIMEOUT => 20,
        ];

        if ($jwt = $this->generateJwt()) {
            $requestOptions = array_merge(
                ['headers' => ['Authorization' => sprintf('Bearer %s', $jwt)]],
                $requestOptions
            );
        }

        return $requestOptions;
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

    protected function getActionUri(): string
    {
        return '/public/promotion/basket';
    }

    /**
     * @return void
     * @throws BasketException
     */
    public function send(): void
    {
        try {
            parent::send();
        } catch (ConnectException $exception) {
            throw new BasketException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        } catch (RequestException $exception) {
            $this->response = $exception->getResponse();
        } catch (GuzzleException | Exception $exception) {
            throw new BasketException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }

    /**
     * @param $responseContent
     * @return BasketModelResponse
     */
    public function handleSuccess($responseContent): BasketModelResponse
    {
        return $this->serializer->deserialize($responseContent, BasketModelResponse::class, 'json');
    }
}
