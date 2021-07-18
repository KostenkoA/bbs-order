<?php

namespace App\Processor;

use App\DTO\UserOrderItemCheck;
use App\Producer\UserHasItemProducer;
use App\Service\Order\OrderItemService;
use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Interop\Queue\PsrProcessor;
use \Exception;

class UserHasItemProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var OrderItemService
     */
    protected $service;

    /**
     * @var UserHasItemProducer
     */
    protected $producer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * UserHasProductProcessor constructor.
     *
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @param OrderItemService $service
     * @param UserHasItemProducer $producer
     */
    public function __construct(
        LoggerInterface $logger,
        SerializerInterface $serializer,
        OrderItemService $service,
        UserHasItemProducer $producer
    ) {
        $this->logger = $logger;
        $this->service = $service;
        $this->serializer = $serializer;
        $this->producer = $producer;
    }

    /**
     * @param PsrMessage $message
     * @param PsrContext $context
     * @return object|string
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        /** @var UserOrderItemCheck $dto */
        $dto = $this->serializer->deserialize($message->getBody(), UserOrderItemCheck::class, 'json');

        try {
            $dto->exist = $this->service->checkProductItemExist($dto->project, $dto->userId, $dto->productSlug);
            $this->producer->sendCheck($dto, (string)$dto->queue, (string)$dto->processor);
        } catch (Exception $e) {
            $this->logger->error(
                $e->getMessage(),
                ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]
            );
        }

        return self::ACK;
    }

    /**
     * @return array|string
     */
    public static function getSubscribedCommand()
    {
        return [
            'processorName' => 'bbs-order.user.product.processor',
            'queueName' => sprintf('%s-bbs-order-external', getenv('APP_ENV')),
            'queueNameHardcoded' => true,
        ];
    }
}
