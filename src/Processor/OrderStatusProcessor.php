<?php

namespace App\Processor;

use App\DTO\OneC\OrderStatus1C;
use App\Service\Order\OrderStatusService;
use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Interop\Queue\PsrProcessor;
use \Exception;

class OrderStatusProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var string
     */
    protected $env;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var OrderStatusService
     */
    protected $service;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * OrderUpdateProcessor constructor.
     *
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @param OrderStatusService $service
     */
    public function __construct(LoggerInterface $logger, SerializerInterface $serializer, OrderStatusService $service)
    {
        $this->logger = $logger;
        $this->service = $service;
        $this->serializer = $serializer;
    }

    /**
     * @param PsrMessage $message
     * @param PsrContext $context
     * @return object|string
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        /** @var OrderStatus1C $order */
        $order = $this->serializer->deserialize($message->getBody(), OrderStatus1C::class, 'json');

        try {
            $this->service->statusFrom1C($order);
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
            'processorName' => 'bbs-order.order-status.update.processor',
            'queueName' => sprintf('%s-bbs-order-external', getenv('APP_ENV')),
            'queueNameHardcoded' => true
        ];
    }
}
