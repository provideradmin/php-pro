<?php

namespace App\CarMaster\Entity\Exceptions;

use Exception;

class InventoryException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
