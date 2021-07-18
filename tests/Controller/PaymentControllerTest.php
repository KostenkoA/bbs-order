<?php

namespace App\Tests\Controller;

use App\Component\Payment\Method\Fondy;
use App\Entity\PaymentStatusInterface;
use App\Tests\Helper\ApiTrait;
use App\Tests\Helper\OrderTrait;
use App\Tests\Helper\PaymentTrait;
use App\Tests\Model\CheckoutPaymentTest;
use App\Tests\Model\NewPaymentTest;
use Symfony\Component\HttpFoundation\Response;

class PaymentControllerTest extends AbstractController
{
    use OrderTrait;
    use PaymentTrait;
    use ApiTrait;

    public function testCreatePayment()
    {
        $this->mockPaymentModel();
        $order = $this->createOrder();
        $this->createOrderItem($order, 'internal_internal', 'product_slug', 3990.0);
        $this->recalculateOrderCost($order);

        $client = $this->createRequestWithToken(
            'POST',
            sprintf('/public/payment/new/%s', $order->getHash())
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertNotEmpty($client->getResponse()->headers->get('location'));
    }

    public function testCheckResponse()
    {
        $paymentStatus = PaymentStatusInterface::STATUS_APPROVED;

        $this->mockPaymentModel($paymentStatus);

        $order = $this->createOrder();
        $payment = $this->createPayment($order);

        $client = $this->createRequestWithToken(
            'POST',
            sprintf('/public/payment/checkout/%s', $payment->getHash())
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $response = $this->getContent($client);

        $this->assertArrayHasKey('hash', $response);
        $this->assertArrayHasKey('number', $response);
        $this->assertArrayHasKey('method', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertEquals($response['status'], $paymentStatus);
    }

    public function testCheckGetResponse()
    {
        $paymentStatus = PaymentStatusInterface::STATUS_APPROVED;

        $this->mockPaymentModel($paymentStatus);

        $order = $this->createOrder();
        $payment = $this->createPayment($order);

        $client = $this->createRequestWithToken(
            'GET',
            sprintf('/public/payment/checkout/%s', $payment->getHash())
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $response = $this->getContent($client);

        $this->assertArrayHasKey('hash', $response);
        $this->assertArrayHasKey('number', $response);
        $this->assertArrayHasKey('method', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertEquals($response['status'], $paymentStatus);
    }

    private function mockPaymentModel(int $paymentStatus = PaymentStatusInterface::STATUS_APPROVED)
    {
        $service = $this->getMockBuilder(Fondy::class)->disableOriginalConstructor()->getMock();
        $service->method('newPayment')->willReturn(new NewPaymentTest());
        $service->method('checkPayment')->willReturn(new CheckoutPaymentTest());

        $this->getContainer()->set(sprintf('test.%s', Fondy::class), $service);
    }
}
