<?php

namespace GogStore\Log\MessageDecorator;

use GogStore\Log\LoggerDecorator;

class TypeInfo extends LoggerDecorator
{
    function log(string $message): void
    {
        $this->logger->log("[info      ] $message");
    }
}