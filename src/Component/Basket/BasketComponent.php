<?php

namespace App\Component\Basket;

use App\Component\Basket\DTO\BasketModelResponse;
use App\Component\RequestResponseException;
use App\DTO\Basket\Basket;
use App\DTO\BasketChecked\BasketChecked;

class BasketComponent
{
    /**
     * @var CalculateBasketRequest
     */
    private $calculateRequest;

    /**
     * @var BasketRequestBuilder
     */
    private $requestBuilder;

    public function __construct(CalculateBasketRequest $calculateBasketRequest, BasketRequestBuilder $requestBuilder)
    {
        $this->calculateRequest = $calculateBasketRequest;
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * @param Basket $basket
     * @param BasketChecked $basketChecked
     * @return BasketModelResponse
     * @throws BasketException
     * @throws RequestResponseException
     */
    public function calculateFromBasket(Basket $basket, BasketChecked $basketChecked): BasketModelResponse
    {
        $this->calculateRequest->setBasketModel($this->requestBuilder->buildFromBasket($basket, $basketChecked));
        $this->calculateRequest->send();

        /** @var BasketModelResponse $response */
        $response = $this->calculateRequest->handleResponse();

        return $response;
    }
}
