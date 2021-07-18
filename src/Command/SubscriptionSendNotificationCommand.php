<?php

namespace App\Command;

use App\Event\SubscriptionEvent;
use App\Service\Subscription\SubscriptionService;
use DateTime;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SubscriptionSendNotificationCommand extends Command
{
    protected static $defaultName = 'subscription:send-notification';

    /**
     * @var SubscriptionService
     */
    private $service;

    public function __construct(string $name = null, SubscriptionService $service)
    {
        parent::__construct($name);

        $this->service = $service;
    }

    protected function configure()
    {
        $this
            ->setDescription('Send notification for next day subscription order')
            ->addArgument('forDate', InputArgument::OPTIONAL, 'create order for date');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $forDate = new DateTime($input->getArgument('forDate') ?? 'now + 1 day');
        $forDate->setTime(0, 0, 0);

        $this->service->eventForDate($forDate, SubscriptionEvent::EVENT_PREORDER_NOTIFICATION);
    }
}
