<?php

namespace GogStore\Log\MessageDecorator;

use GogStore\Log\Logger;
use GogStore\Log\LoggerDecorator;

class AddException extends LoggerDecorator
{
    private \Throwable $exception;

    public function __construct(\Throwable $exception, Logger $logger)
    {
        $this->exception = $exception;
        parent::__construct($logger);
    }

    function log(string $message): void
    {
        $this->logger->log($message . PHP_EOL . $this->exception->getTraceAsString());
    }
}