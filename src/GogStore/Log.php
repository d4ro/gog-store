<?php

namespace GogStore;

use DateTime;
use GogStore\Log\Logger;
use GogStore\Log\LoggerException;
use GogStore\Log\MessageDecorator\AddDate;
use GogStore\Log\MessageDecorator\AddException;
use GogStore\Log\MessageDecorator\AddTag;
use GogStore\Log\MessageDecorator\TypeError;
use GogStore\Log\MessageDecorator\TypeInfo;
use GogStore\Log\MessageDecorator\TypeWarn;
use GogStore\Log\Method\File;
use GogStore\Pattern\Singleton;

class Log
{
    private static ?Log $instance = null;

    public static function getInstance(): Log
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private DateTime $startDate;
    private Logger $concreteLogger;

    /**
     * Private log constructor.
     * Create log instance via getInstance() method.
     *
     * @throws LoggerException
     */
    private function __construct()
    {
        $this->startDate = new DateTime();
        $this->concreteLogger = $this->loggerFactory(Config::getInstance());
    }

    /**
     * Creates an instance of a concrete logger by a method included in a config.
     * Throws an exception if supplied method is not supported.
     *
     * @param Config $config
     * @return Logger
     * @throws LoggerException
     */
    private function loggerFactory(Config $config): Logger
    {
        switch ($config->getValue('LOG_METHOD')) {
            case 'file':
                return new File(APP_PATH
                    . DIRECTORY_SEPARATOR
                    . $config->getValue('LOG_PATH')
                    . 'log_'
                    . $this->startDate->format('Y-m-d_H-i-s')
                    . '.txt');
            default:
                throw new LoggerException("Log method '{$config->getValue('LOG_METHOD')}' is not supported.");

        }
    }

    public function error($tag, $message, \Throwable $exception = null): void
    {
        $logger = new AddTag($tag, new TypeError(new AddDate($this->concreteLogger)));
        if (null !== $exception)
            $logger = new AddException($exception, $logger);

        $logger->log($message);
    }

    public function warn($tag, $message): void
    {
        (new AddTag($tag, new TypeWarn(new AddDate($this->concreteLogger))))->log($message);
    }

    public function info($tag, $message): void
    {
        (new AddTag($tag, new TypeInfo(new AddDate($this->concreteLogger))))->log($message);
    }
}