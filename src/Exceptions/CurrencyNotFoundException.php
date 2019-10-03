<?php

namespace GNAHotelSolutions\CurrencyConverter\Exceptions;

use Exception;

class CurrencyNotFoundException extends Exception
{

    public function __construct(string $currency)
    {
        parent::__construct("Currency {$currency} does not found.");
    }
}