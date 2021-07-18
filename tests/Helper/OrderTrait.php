<?php

namespace App\Tests\Helper;

use App\DTO\NewOrder;
use App\DTO\Basket\BasketItem;
use App\Entity\DeliveryTypeInterface;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\PaymentTypeInterface;
use App\Component\Product\Response\Product;

trait OrderTrait
{
    public function checkOrderCreate(array $content): void
    {
        $this->assertArrayHasKey('hash', $content);
        $this->assertArrayHasKey('number', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('cost', $content);
    }

    public function checkOrderList(array $response): void
    {
        $this->assertArrayHasKey('offset', $response);
        $this->assertArrayHasKey('totalCount', $response);
        $this->assertArrayHasKey('data', $response);

        foreach ($response['data'] as $item) {
            $this->assertArrayHasKey('hash', $item);
            $this->assertArrayHasKey('number', $item);
            $this->assertArrayHasKey('status', $item);
            $this->assertArrayHasKey('cost', $item);
            $this->assertArrayHasKey('createdAt', $item);
        }
    }

    public function checkOrderInfo(array $response): void
    {
        $this->assertArrayHasKey('hash', $response);
        $this->assertArrayHasKey('number', $response);
        $this->assertArrayHasKey('firstName', $response);
        $this->assertArrayHasKey('lastName', $response);
        $this->assertArrayHasKey('middleName', $response);
        $this->assertArrayHasKey('deliveryType', $response);
        $this->assertArrayHasKey('deliveryBranch', $response);
        $this->assertArrayHasKey('deliveryCarrier', $response);
        $this->assertArrayHasKey('paymentType', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('deliveryStatus', $response);
        $this->assertArrayHasKey('paymentStatus', $response);
        $this->assertArrayHasKey('region', $response);
        $this->assertArrayHasKey('district', $response);
        $this->assertArrayHasKey('city', $response);
        $this->assertArrayHasKey('streetType', $response);
        $this->assertArrayHasKey('street', $response);
        $this->assertArrayHasKey('building', $response);
        $this->assertArrayHasKey('apartment', $response);
        $this->assertArrayHasKey('comment', $response);
        $this->assertArrayHasKey('price', $response);
        $this->assertArrayHasKey('deliveryPrice', $response);
        $this->assertArrayHasKey('discountAmount', $response);
        $this->assertArrayHasKey('voucherAmount', $response);
        $this->assertArrayHasKey('cost', $response);
        $this->assertArrayHasKey('orderItems', $response);

        foreach ($response['orderItems'] as $orderItem) {
            $this->assertArrayHasKey('productId', $orderItem);
            $this->assertArrayHasKey('internalId', $orderItem);
            $this->assertArrayHasKey('displayArticle', $orderItem);
            $this->assertArrayHasKey('category', $orderItem);
            $this->assertArrayHasKey('folderCategory', $orderItem);
            $this->assertArrayHasKey('brand', $orderItem);
            $this->assertArrayHasKey('recommendedPrice', $orderItem);
            $this->assertArrayHasKey('deliveryDate', $orderItem);
            $this->assertArrayHasKey('reserveState', $orderItem);
            $this->assertArrayHasKey('slug', $orderItem);
            $this->assertArrayHasKey('price', $orderItem);
            $this->assertArrayHasKey('title', $orderItem);
            $this->assertArrayHasKey('titleUkr', $orderItem);
            $this->assertArrayHasKey('quantity', $orderItem);
            $this->assertArrayHasKey('totalPrice', $orderItem);
            $this->assertArrayHasKey('colorPresentation', $orderItem);
            $this->assertArrayHasKey('sizePresentation', $orderItem);
            $this->assertArrayHasKey('ageCategory', $orderItem);
            $this->assertArrayHasKey('images', $orderItem);
            $this->assertArrayHasKey('createdAt', $orderItem);
        }
    }


    public function createOrder(
        ?int $userRef = 1,
        string $projectName = 'bbs',
        string $firstName = 'testFirstName',
        string $lastName = 'testLastName',
        string $middleName = 'testMiddleName',
        string $phone = '380111111111',
        string $email = 'test@test.email',
        int $deliveryType = DeliveryTypeInterface::DELIVERY_BRANCH,
        int $paymentType = PaymentTypeInterface::PAYMENT_CARD,
        string $city = 'Kiyv'
    ) {
        $orderDTO = new NewOrder();
        $orderDTO->userRef = $userRef;
        $orderDTO->project = $projectName;
        $orderDTO->firstName = $firstName;
        $orderDTO->lastName = $lastName;
        $orderDTO->middleName = $middleName;
        $orderDTO->phone = $phone;
        $orderDTO->email = $email;
        $orderDTO->deliveryType = $deliveryType;
        $orderDTO->paymentType = $paymentType;
        $orderDTO->city = $city;

        $orderEntity = new Order();
        $orderEntity->fillFromNewOrder($orderDTO, !$userRef);

        $this->saveEntity($orderEntity);

        return $orderEntity;
    }

    public function createOrderItem(
        Order $order,
        string $intervalId = 'internalInterval',
        string $slug = 'productSlug',
        float $price = 0
    ): void {
        $newOrderItem = new BasketItem();
        $newOrderItem->internalId = $intervalId;
        $newOrderItem->quantity = 1;


        $orderItem = new OrderItem($intervalId, 1, $order);

        $product = new Product();
        $product->intervalId = $intervalId;
        $product->slug = $slug;
        $product->sellingPrice = $price;

        $orderItem->fillFromProduct($product);

        $em = $this->getEntityManager();
        $em->persist($orderItem);
        $em->flush();
        $em->refresh($order);
    }

    public function recalculateOrderCost(Order $order): void
    {
        $order->calculatePrice();
        $order->calculateCost();
        $this->saveEntity($order);
    }

    private function saveEntity($entity, $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        if ($flush) {
            $em->flush();
            $em->refresh($entity);
        }
    }
}
