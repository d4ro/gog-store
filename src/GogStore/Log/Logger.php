<?php

namespace GogStore\Log;

interface Logger
{
    function log(string $message): void;
}