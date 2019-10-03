<?php

namespace GNAHotelSolutions\CurrencyConverter;

use GNAHotelSolutions\CurrencyConverter\Exceptions\CurrencyNotFoundException;
use GNAHotelSolutions\CurrencyConverter\Contracts\CurrenciesRepositoryContract;

class CurrenciesRepository implements CurrenciesRepositoryContract
{
    /** @var array */
    private $currencies;

    public function __construct(array $currencies)
    {
        foreach ($currencies as $currency) {
            $this->append($currency);
        }
    }

    public function get(string $currency): ?Currency
    {
        if (! isset($this->currencies[$currency])) {
            throw new CurrencyNotFoundException($currency);
        }

        return $this->currencies[$currency];
    }

    public function append(Currency $currency): void
    {
        $this->currencies[$currency->name()] = $currency;
    }

    public function has(string $currency): bool
    {
        return isset($this->currencies[$currency]);
    }
}
