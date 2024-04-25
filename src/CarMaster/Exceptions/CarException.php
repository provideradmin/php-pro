<?php

namespace CarMaster\Exceptions;

use Exception;

class CarException extends Exception
{
    protected $number;

    public function __construct(string $message, string $number)
    {
        parent::__construct($message);
        $this->number = $number;
    }

    public function getNumber(): string
    {
        return $this->number;
    }
}

