<?php

namespace App\Tests\Controller;

use App\Entity\DeliveryTypeInterface;
use App\Producer\Order1CProducer;
use App\Service\User\UserService;
use App\Tests\Helper\ApiTrait;
use App\Tests\Helper\OrderTrait;
use App\Tests\Helper\ProductTrait;
use Symfony\Component\HttpFoundation\Response;

class OrderControllerTest extends AbstractController
{
    use ApiTrait;
    use OrderTrait;
    use ProductTrait;

    private function getOrderParams(): array
    {
        return [
            'firstName' => 'testFirstName',
            'lastName' => 'testLastName',
            'middleName' => 'testMiddleName',
            'phone' => '380111111111',
            'email' => 'email@test.com',
            'paymentType' => 1,
            'comment' => null,
            'callBack' => 1,
            'items' => [
                [
                    'internalId' => 'f8e742b5-b8bb-11e7-8137-0050568e2bbe',
                    'quantity' => 2,
                ],
            ],
        ];
    }

    private function getDeliveryBranchParams(): array
    {
        return [
            'deliveryType' => DeliveryTypeInterface::DELIVERY_BRANCH,
            'deliveryCarrier' => 2,
            'region' => null,
            'district' => null,
            'city' => 'Киев',
            'deliveryBranch' => null,
        ];
    }

    private function getDeliveryAddressParams(): array
    {
        return [
            'deliveryType' => DeliveryTypeInterface::DELIVERY_ADDRESS,
            'deliveryCarrier' => 0,
            'region' => null,
            'district' => null,
            'city' => 'Киев',
            'streetType' => null,
            'street' => null,
            'building' => null,
            'apartment' => null,
        ];
    }

    public function testCreateOrderDeliveryAddress(): void
    {
        $this->mockProductService();

        $postData = array_merge($this->getOrderParams(), $this->getDeliveryAddressParams());

        $client = $this->createRequestWithToken('POST', '/public/order', [], $postData);

        $this->checkJsonResponse($client, Response::HTTP_CREATED);
        $this->checkOrderCreate($this->getContent($client));
    }

    public function testCreateOrderDeliveryBranch(): void
    {
        $this->mockProductService();

        $postData = array_merge($this->getOrderParams(), $this->getDeliveryBranchParams());

        $client = $this->createRequestWithToken('POST', '/public/order', [], $postData);

        $this->checkJsonResponse($client, Response::HTTP_CREATED);
        $this->checkOrderCreate($this->getContent($client));
    }

    public function testCreateOrderByAnon(): void
    {
        $this->mockProductService();
        $this->mockUserService();

        $postData = array_merge($this->getOrderParams(), $this->getDeliveryBranchParams());

        $client = $this->createRequestWithoutToken('POST', '/public/anon/order', [], $postData);

        $this->checkJsonResponse($client, Response::HTTP_CREATED);
        $this->checkOrderCreate($this->getContent($client));
    }

    public function testCreateOrderByAnonRegister(): void
    {
        $this->mockProductService();
        $this->mockUserService();

        $postData = array_merge($this->getOrderParams(), $this->getDeliveryBranchParams(), ['userLanguageId' => 1]);

        $client = $this->createRequestWithoutToken('POST', '/public/anon/order/register', [], $postData);

        $this->checkJsonResponse($client, Response::HTTP_CREATED);
        $this->checkOrderCreate($this->getContent($client));
    }

    public function mockUserService(): void
    {
        $service = $this->getMockBuilder(UserService::class)->disableOriginalConstructor()->getMock();
        $service->method('register')->willReturn(true);
        $service->method('sendForAttach')->willReturn(true);
        $this->getContainer()->set(sprintf('test.%s', UserService::class), $service);
    }

    public function mockOrder1CService(): void
    {
        $service = $this->getMockBuilder(Order1CProducer::class)->disableOriginalConstructor()->getMock();
        $service->method('sendNewOrder')->willReturn(true);
        $this->getContainer()->set(sprintf('test.%s', Order1CProducer::class), $service);
    }

    public function testGetListBbs(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->createOrder();
        }

        $client = $this->createRequestWithToken(
            'GET',
            '/public/order',
            ['pagination' => ['page' => 1, 'limit' => 10]]
        );
        $this->checkJsonResponse($client, Response::HTTP_OK);
        $this->checkOrderList($this->getContent($client));
    }

    public function testGetListOtherProject(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->createOrder(1, 'test-other-project');
        }

        $client = $this->createRequestWithToken(
            'GET',
            '/public/order',
            ['pagination' => ['page' => 1, 'limit' => 10]],
            [],
            'test-other-project'
        );
        $this->checkJsonResponse($client, Response::HTTP_OK);

        $content = $this->getContent($client);
        $this->checkOrderList($content);

        $this->assertCount(10, $content['data']);
    }

    public function testGetListOtherUser(): void
    {
        $userRef = 2;
        for ($i = 1; $i <= 8; $i++) {
            $this->createOrder($userRef, 'test-other-user');
        }

        $client = $this->createRequestWithToken(
            'GET',
            '/public/order',
            ['pagination' => ['page' => 1, 'limit' => 10]],
            [],
            'test-other-user',
            $userRef
        );
        $this->checkJsonResponse($client, Response::HTTP_OK);

        $content = $this->getContent($client);
        $this->checkOrderList($content);

        $this->assertCount(8, $content['data']);
    }

    public function testFindOrder(): void
    {
        $order = $this->createOrder();
        $this->createOrderItem($order);

        $client = $this->createRequestWithToken('GET', sprintf('/public/order/%s', $order->getHash()));

        $this->checkJsonResponse($client, Response::HTTP_OK);
        $this->checkOrderInfo($this->getContent($client));
    }

    public function testFindOrderByAnon(): void
    {
        $order = $this->createOrder(null);
        $this->createOrderItem($order);

        $client = $this->createRequestWithToken('GET', sprintf('/public/anon/order/%s', $order->getHash()));

        $this->checkJsonResponse($client, Response::HTTP_OK);
        $this->checkOrderInfo($this->getContent($client));
    }

    public function testAdminGetOrderList(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->createOrder();
        }

        $client = $this->createRequestWithToken(
            'GET',
            '/admin/order',
            ['pagination' => ['page' => 1, 'limit' => 10]]
        );

        $this->checkJsonResponse($client, Response::HTTP_OK);
        $this->checkOrderList($this->getContent($client));
    }

    public function testAdminGetOrder(): void
    {
        $order = $this->createOrder();
        $this->createOrderItem($order);

        $client = $this->createRequestWithToken('GET', sprintf('/admin/order/%d', $order->getId()));

        $this->checkJsonResponse($client, Response::HTTP_OK);
        $this->checkOrderInfo($this->getContent($client));
    }

    public function testAdminCreateOrderDeliveryBranch(): void
    {
        $this->mockProductService();
        $this->mockUserService();

        $postData = array_merge($this->getOrderParams(), $this->getDeliveryBranchParams(), ['userRef' => 2]);

        $client = $this->createRequestWithToken('POST', '/admin/order/create/registered', [], $postData);

        $this->checkJsonResponse($client, Response::HTTP_CREATED);
        $this->checkOrderCreate($this->getContent($client));
    }

    public function testAdminCreateOrderDeliveryAddress(): void
    {
        $this->mockProductService();
        $this->mockUserService();

        $postData = array_merge($this->getOrderParams(), $this->getDeliveryAddressParams(), ['userRef' => 2]);

        $client = $this->createRequestWithToken('POST', '/admin/order/create/registered', [], $postData);

        $this->checkJsonResponse($client, Response::HTTP_CREATED);
        $this->checkOrderCreate($this->getContent($client));
    }

    public function testAdminCreateOrderByAnonRegisterDeliveryBranch(): void
    {
        $this->mockProductService();
        $this->mockUserService();

        $postData = array_merge($this->getOrderParams(), $this->getDeliveryBranchParams(), ['userLanguageId' => 1]);

        $client = $this->createRequestWithToken('POST', '/admin/order/create/autoregister', [], $postData);

        $this->checkJsonResponse($client, Response::HTTP_CREATED);
        $this->checkOrderCreate($this->getContent($client));
    }

    public function testAdminSend1cOrder(): void
    {
        $order = $this->createOrder();
        $this->createOrderItem($order);
        $this->mockOrder1CService();

        $client = $this->createRequestWithToken('POST', sprintf('/admin/order/send-1c/%d', $order->getId()));

        $this->checkJsonResponse($client, Response::HTTP_OK);
    }
}
