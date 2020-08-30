<?php

namespace AG\WeatherNow\API;

defined('ABSPATH') or die();

/**
 * Define a custom exception class
 */
class APIQueryException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function recoveryFunction()
    {
    }
}
