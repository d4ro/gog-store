<?php

namespace GogStore\Log;

abstract class LoggerDecorator implements Logger
{
    protected Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
}