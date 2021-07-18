<?php

namespace App\Component\Notification;

abstract class NotificationAbstract
{
    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $project;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string[]
     */
    private $variables = [];

    /**
     * EmailNotificationDTO constructor.
     * @param string $to
     * @param string $project
     * @param string $language
     * @param string[] $variables
     */
    public function __construct(string $to, string $project, string $language, array $variables)
    {
        $this->to = $to;
        $this->project = $project;
        $this->language = $language;
        $this->variables = $variables;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return string[]
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return string
     */
    abstract public function getTemplate(): string;
}
