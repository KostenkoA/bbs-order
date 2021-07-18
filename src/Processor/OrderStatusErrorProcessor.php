<?php

namespace App\Processor;

use App\DTO\OneC\OrderStatusError1C;
use App\Service\Order\OrderStatusService;
use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Interop\Queue\PsrProcessor;
use \Exception;

class OrderStatusErrorProcessor implements PsrProcessor, CommandSubscriberInterface
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
        /** @var OrderStatusError1C $orderError */
        $orderError = $this->serializer->deserialize($message->getBody(), OrderStatusError1C::class, 'json');

        try {
            $this->service->statusErrorFrom1C($orderError);
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
            'processorName' => 'bbs-order.order-status.error.processor',
            'queueName' => sprintf('%s-bbs-order-external', getenv('APP_ENV')),
            'queueNameHardcoded' => true
        ];
    }
}
