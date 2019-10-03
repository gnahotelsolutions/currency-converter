<?php

namespace GNAHotelSolutions\CurrencyConverter\Contracts;

use GNAHotelSolutions\CurrencyConverter\Currency;

interface CurrenciesRepositoryContract
{
    public function get(string $currency): ?Currency;

    public function has(string $currency): bool;

    public function append(Currency $currency): void;
}
