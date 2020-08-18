<?php

namespace GogStore\Log\MessageDecorator;

use GogStore\Log\Logger;
use GogStore\Log\LoggerDecorator;

class AddTag extends LoggerDecorator
{
    private string $tag = '';

    public function __construct(string $tag, Logger $logger)
    {
        $this->tag = $tag;
        parent::__construct($logger);
    }

    function log(string $message): void
    {
        $this->logger->log("[{$this->tag}] $message");
    }
}