<?php

namespace GNAHotelSolutions\CurrencyConverter;

use GNAHotelSolutions\CurrencyConverter\Contracts\CurrenciesRepositoryContract;

class Converter
{
    protected Price $price;
    protected Currency $currency;

    public function __construct(protected Currency $base, protected CurrenciesRepositoryContract $currencies)
    {
    }

    /**
     * Get the price we want to convert.
     */
    public function price(): Price
    {
        return $this->price;
    }

    /**
     * Get the currency we want the price converted to.
     */
    public function currency(): Currency
    {
        return $this->currency;
    }

    /**
     * Get the base currency we use to convert from one ratio to another.
     */
    public function base(): Currency
    {
        return $this->base;
    }

    /**
     * Set the original price to be converted.
     */
    public function from(Price $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Set the currency that will be used for the conversion.
     */
    public function to(Currency|string $currency): self
    {
        $this->currency = $currency instanceof Currency
            ? $currency
            : $this->currencies->get(strtoupper($currency));

        return $this;
    }

    /**
     * Convert the price to the currency. It will convert to the base currency first if needed.
     */
    public function convert(): Price
    {
        if ($this->isConvertingToSameCurrency()) {
            return $this->price;
        }

        if ($this->isConvertingToBase()) {
            return $this->convertToBase();
        }

        if ($this->isConvertingWithoutUsingBase()) {
            $this->price = (new self($this->base(), $this->currencies))->from($this->price)->convertToBase();
        }

        return new Price($this->convertAmount($this->currency()), $this->currency()->name());
    }

    /**
     * Convert the amount performing different operations depending on the currency we want.
     */
    public function convertAmount(Currency $currency): float|int
    {
        $ratio = $this->currency() && $this->currency()->is($this->base()->name())
            ? $this->base()->ratio() / $currency->ratio()
            : $currency->ratio();

        return round($this->price()->amount() * $ratio, $currency->decimals());
    }

    /**
     * Convert the current price to base currency.
     */
    public function convertToBase(): Price
    {
        $this->currency = $this->base();

        $amount = $this->convertAmount($this->currencies->get($this->price->currency()));

        return new Price($amount, $this->base()->name());
    }

    /**
     * Check from and to use the same currency.
     */
    protected function isConvertingToSameCurrency(): bool
    {
        return $this->currency()->is($this->price->currency());
    }

    /**
     * Check is trying to convert to the base currency.
     */
    protected function isConvertingToBase(): bool
    {
        return $this->base()->is($this->currency()->name());
    }

    /**
     * Check neither from nor to are the base currency.
     */
    protected function isConvertingWithoutUsingBase(): bool
    {
        return !$this->base()->is($this->price->currency(), $this->currency()->name());
    }
}
