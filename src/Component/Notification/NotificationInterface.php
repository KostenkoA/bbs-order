<?php

namespace App\Component\Notification;

interface NotificationInterface
{
    /**
     * @return string
     */
    public function getTo(): string;

    /**
     * @return string
     */
    public function getProject(): string;

    /**
     * @return string
     */
    public function getTemplate(): string;

    /**
     * @return string
     */
    public function getLanguage(): string;

    /**
     * @return string[]
     */
    public function getVariables(): array;
}
