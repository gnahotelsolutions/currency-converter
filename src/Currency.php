<?php

namespace GNAHotelSolutions\CurrencyConverter;

class Currency
{
    private string $name;

    private float $ratio;

    private int $decimals;

    public function __construct(string $name, float $ratio, int $decimals)
    {
        $this->name = $name;
        $this->ratio = $ratio;
        $this->decimals = $decimals;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function ratio(): float
    {
        return $this->ratio;
    }

    public function decimals(): int
    {
        return $this->decimals;
    }

    public function is(...$currencies): bool
    {
        foreach ($currencies as $currency) {
            if ($this->name() === $currency) {
                return true;
            }
        }

        return false;
    }
}
