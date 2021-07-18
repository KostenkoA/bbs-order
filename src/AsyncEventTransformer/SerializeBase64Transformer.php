<?php

namespace App\AsyncEventTransformer;

use Enqueue\AsyncEventDispatcher\EventTransformer;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Symfony\Component\EventDispatcher\Event;

class SerializeBase64Transformer implements EventTransformer
{
    /**
     * @var PsrContext
     */
    private $context;

    /**
     * @param PsrContext $context
     */
    public function __construct(PsrContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function toMessage($eventName, Event $event = null)
    {
        return $this->context->createMessage(base64_encode(gzcompress(serialize($event))));
    }

    /**
     * {@inheritdoc}
     */
    public function toEvent($eventName, PsrMessage $message)
    {
        return unserialize(gzuncompress(base64_decode($message->getBody())));
    }
}
