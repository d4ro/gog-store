<?php

namespace GogStore\Log\Method;

use GogStore\Log\Logger;

class File implements Logger
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    function log(string $message): void
    {
        file_put_contents($this->filename, $message . PHP_EOL, FILE_APPEND);
    }
}