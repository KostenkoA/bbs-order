<?php

namespace App\Tests\Controller;

use App\Tests\Helper\ApiTrait;
use App\Tests\Helper\BasketTrait;
use App\Tests\Helper\ProductTrait;
use Symfony\Component\HttpFoundation\Response;

class BasketControllerTest extends AbstractController
{
    use ApiTrait;
    use BasketTrait;
    use ProductTrait;

    public function getBasketParams()
    {
        return [
            'items' => [
                [
                    'internalId' => 'f8e742b5-b8bb-11e7-8137-0050568e2bbe',
                    'quantity' => 1,
                ]
            ]
        ];
    }

    public function testCheckBasket(): void
    {
        $this->mockProductService();

        $client = $this->createRequestWithToken('POST', '/public/basket/check', [], $this->getBasketParams());

        $this->checkJsonResponse($client, Response::HTTP_OK);
        $this->checkBasketInfo($this->getContent($client));
    }
}
