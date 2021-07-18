<?php

namespace App\Processor;

use App\DTO\AttachOrdersByPhone;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;

class OrderAttachByPhoneProcessor implements PsrProcessor, CommandSubscriberInterface
{
    public const QUEUE_NAME = 'bbs-order-external';

    public const PROCESSOR_NAME = 'bbs-order.attach.by-phone.processor';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * OrderAttachByPhoneProcessor constructor.
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        LoggerInterface $logger,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $this->logger = $logger;
        $this->em = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @param PsrMessage $message
     * @param PsrContext $context
     * @return object|string
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        /** @var AttachOrdersByPhone $attachDTO */
        $attachDTO = $this->serializer->deserialize($message->getBody(), AttachOrdersByPhone::class, 'json');

        try {
            /** @var OrderRepository $repository */
            $repository = $this->em->getRepository(Order::class);
            $repository->attachOrdersByPhone($attachDTO->phone, $attachDTO->project, $attachDTO->user_id);
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
            'processorName' => self::PROCESSOR_NAME,
            'queueName' => sprintf('%s-%s', getenv('APP_ENV'), self::QUEUE_NAME),
            'queueNameHardcoded' => true,
        ];
    }
}
