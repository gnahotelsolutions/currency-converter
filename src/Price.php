<?php

namespace GNAHotelSolutions\CurrencyConverter;

class Price
{
    protected float $amount;

    protected string $currency;

    const CURRENCY_SYMBOLS = [
        '€' => 'EUR',
        '$' => 'USD',
        '£' => 'GBP',
        // TODO: Add more currency symbols and their codes
    ];

    public function __construct($amount, string $currency)
    {
        $this->currency = $this->parseCurrency($currency);

        $this->amount = $this->parseAmount($amount);
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    private function parseCurrency($currency): string
    {
        if (isset(self::CURRENCY_SYMBOLS[$currency])) {
            return self::CURRENCY_SYMBOLS[$currency];
        }

        return strtoupper($currency);
    }

    protected function parseAmount($amount): float
    {
        return (float) $amount;
    }

    public function __toString()
    {
        return "{$this->amount()} {$this->currency()}";
    }
}
