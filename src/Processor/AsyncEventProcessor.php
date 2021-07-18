<?php

namespace App\Processor;

use Enqueue\AsyncEventDispatcher\AsyncProcessor;
use Enqueue\AsyncEventDispatcher\Registry;
use Enqueue\Consumption\Result;
use Exception;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AsyncEventProcessor extends AsyncProcessor
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AsyncEventProcessor constructor.
     * @param Registry $registry
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(Registry $registry, EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        parent::__construct($registry, $dispatcher);

        $this->logger = $logger;
    }

    /**
     * @param PsrMessage $message
     * @param PsrContext $context
     * @return Result|object|string
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        try {
            return parent::process($message, $context);
        } catch (Exception $e) {
            $this->logger->error(
                $e->getMessage(),
                ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]
            );

            return self::ACK;
        }
    }
}
