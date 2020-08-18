<?php

namespace GogStore\Log\MessageDecorator;

use GogStore\Log\LoggerDecorator;

class TypeError extends LoggerDecorator
{
    function log(string $message): void
    {
        $this->logger->log("[ERROR !!! ] $message");
    }
}