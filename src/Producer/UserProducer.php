<?php

namespace App\Producer;

use App\DTO\UserFind;
use App\Processor\OrderAttachByPhoneProcessor;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;

class UserProducer extends ProducerAbstract
{
    protected const QUEUE_NAME = 'user-external';

    private const FIND_FOR_ATTACH_PROCESSOR = 'bbs-user.find.processor';

    /**
     * @param UserFind $user
     *
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendForAttach(UserFind $user): void
    {
        $user->processor = OrderAttachByPhoneProcessor::PROCESSOR_NAME;
        $user->queue = sprintf('%s-%s', $this->env, OrderAttachByPhoneProcessor::QUEUE_NAME);

        $this->send(
            self::FIND_FOR_ATTACH_PROCESSOR,
            $this->serializer->serialize($user, 'json', ['groups' => 'attach']),
            $this->getQueueName($user->project)
        );
    }

    /**
     * @param string $projectName
     * @return string
     */
    public function getQueueName(string $projectName = 'bbs'): string
    {
        return sprintf('%s-%s-%s', $this->env, $projectName, static::QUEUE_NAME);
    }
}
