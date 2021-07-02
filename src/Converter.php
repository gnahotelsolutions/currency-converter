<?php

namespace GNAHotelSolutions\CurrencyConverter;

use GNAHotelSolutions\CurrencyConverter\Contracts\CurrenciesRepositoryContract;

class Converter
{
    private Price $price;

    private Currency $currency;

    private CurrenciesRepositoryContract $currencies;

    private Currency $base;

    public function __construct(Currency $baseCurrency, CurrenciesRepositoryContract $currencies)
    {
        $this->base = $baseCurrency;

        $this->currencies = $currencies;
    }

    public function price(): Price
    {
        return $this->price;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function base(): Currency
    {
        return $this->base;
    }

    /**
     * Set the original price to be converted.
     *
     * @param Price $price
     * @return $this
     */
    public function from(Price $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Set the currency that will be used for the conversion.
     *
     * @param Currency|string $currency
     * @return $this
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
     *
     * @return Price
     */
    public function convert(): Price
    {
        if ($this->currency()->is($this->price->currency())) {
            return $this->price;
        }

        if ($this->base()->is($this->price->currency(), $this->currency()->name())) {
            $this->price = (new self($this->base(), $this->currencies))->from($this->price)->convertToBase();
        }

        return new Price($this->convertAmount($this->currency()), $this->currency()->name());
    }

    /**
     * Convert the amount performing different operations depending on the currency we want.
     *
     * @param Currency $currency
     * @return float|int
     */
    public function convertAmount(Currency $currency): float|int
    {
        $ratio = $this->currency() && $this->currency()->is($this->base()->name())
            ? $this->base()->ratio() / $currency->ratio()
            : $currency->ratio();

        return round($this->price()->amount() * $ratio, $currency->decimals());
    }

    /**
     * Convert the current price to EUR.
     *
     * @return Price
     */
    public function convertToBase(): Price
    {
        $this->currency = $this->base();

        $amount = $this->convertAmount($this->currencies->get($this->price->currency()));

        return new Price($amount, $this->base()->name());
    }
}
