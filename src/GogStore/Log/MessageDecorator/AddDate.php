<?php

namespace GogStore\Log\MessageDecorator;

use DateTime;
use GogStore\Log\LoggerDecorator;

class AddDate extends LoggerDecorator
{
    function log(string $message): void
    {
        $date = DateTime::createFromFormat('U.u', number_format(microtime(true), 5, '.', ''));
        $this->logger->log("{$date->format('[Y-m-d H:i:s.u]')} $message");
    }
}