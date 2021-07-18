<?php

namespace App\Producer;

use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;
use Interop\Queue\PsrContext;
use Symfony\Component\Serializer\SerializerInterface;
use Enqueue\Client\Config;

abstract class ProducerAbstract
{
    protected const QUEUE_NAME = '';

    protected const TOPIC = '__command__';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var PsrContext
     */
    protected $context;

    /**
     * @var string
     */
    protected $env;

    /**
     * ProducerAbstract constructor.
     *
     * @param PsrContext $context
     * @param SerializerInterface $serializer
     * @param string $env
     */
    public function __construct(PsrContext $context, SerializerInterface $serializer, string $env)
    {
        $this->context = $context;
        $this->serializer = $serializer;
        $this->env = $env;
    }


    /**
     * @param string $processorName
     * @param string $body
     * @param string|null $queueName
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    protected function send(string $processorName, string $body, string $queueName = null): void
    {
        $this->context->createProducer()->send(
            $this->context->createQueue($queueName ?? $this->getQueueName()),
            $this->context->createMessage(
                $body,
                [
                    Config::PARAMETER_TOPIC_NAME => static::TOPIC,
                    Config::PARAMETER_PROCESSOR_NAME => $processorName,
                ]
            )
        );
    }

    /**
     * @return string
     */
    protected function getQueueName(): string
    {
        return sprintf('%s-%s', $this->env, static::QUEUE_NAME);
    }
}
