<?php

namespace GogStore\Log\MessageDecorator;

use GogStore\Log\LoggerDecorator;

class TypeWarn extends LoggerDecorator
{
    function log(string $message): void
    {
        $this->logger->log("[  WARN    ] $message");
    }
}