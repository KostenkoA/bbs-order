<?php

namespace App\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface HttpRequestInterface
{
    public function getResponse(): ?ResponseInterface;

    public function send(): void;
}
