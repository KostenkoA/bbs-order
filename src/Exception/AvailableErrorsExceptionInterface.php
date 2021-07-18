<?php

namespace App\Exception;

interface AvailableErrorsExceptionInterface
{
    public function getErrors(): array;
}
