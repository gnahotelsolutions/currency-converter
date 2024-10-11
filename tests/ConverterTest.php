<?php

namespace Gnahotelsolutions\CurrencyConverter\Tests;

use GNAHotelSolutions\CurrencyConverter\Converter;
use GNAHotelSolutions\CurrencyConverter\CurrenciesRepository;
use GNAHotelSolutions\CurrencyConverter\Currency;
use GNAHotelSolutions\CurrencyConverter\Exceptions\CurrencyNotFoundException;
use GNAHotelSolutions\CurrencyConverter\Price;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    private Converter $converter;

    protected function setUp(): void
    {
        parent::setUp();

        $baseCurrency = new Currency('EUR', 1, 2);

        $repository = new CurrenciesRepository([
            $baseCurrency,
            new Currency('USD', 0.3, 2),
            new Currency('GBP', 5, 2),
        ]);

        $this->converter = new Converter($baseCurrency, $repository);
    }

    public function test_from_price_is_assigned(): void
    {
        $price = new Price(1000, 'EUR');

        $this->converter->from($price);

        $this->assertEquals($price, $this->converter->price());
    }

    public function test_to_currency_is_assigned(): void
    {
        $this->converter->to('USD');

        $this->asserttrue($this->converter->currency()->is('USD'));
    }

    public function test_can_convert_currency_to_base(): void
    {
        $priceEuro = $this->converter->from(new Price(500, 'USD'))->convertToBase();

        $this->assertSame('EUR', $priceEuro->currency());

        $this->assertSame(1666.67, $priceEuro->amount());

        $price = $this->converter->from(new Price(500, 'GBP'))->convertToBase();

        $this->assertSame(100.0, $price->amount());
    }

    public function test_currency_is_converted_through_base(): void
    {
        $price = $this->converter->from(new Price(158.78, 'GBP'))->to('USD')->convert();

        $this->assertEquals(9.53, $price->amount());

        $this->assertSame('USD', $price->currency());
    }

    public function test_currency_is_not_converted_when_from_and_to_are_the_same(): void
    {
        $price = $this->converter->from(new Price(500, 'USD'))->to('USD')->convert();

        $this->assertSame(500.0, $price->amount());

        $this->assertSame('USD', $price->currency());
    }

    public function test_currency_is_converted_to_base_without_calling_specific_method(): void
    {
        $price = $this->converter->from(new Price(500, 'USD'))->to('EUR')->convert();

        $this->assertEquals(1666.67, $price->amount());

        $this->assertSame('EUR', $price->currency());
    }

    public function test_currency_is_converted_from_base_correctly(): void
    {
        $price = $this->converter->from(new Price(100, 'EUR'))->to('USD')->convert();

        $this->assertEquals(30.00, $price->amount());

        $this->assertSame('USD', $price->currency());
    }

    public function test_exception_is_thrown_if_currency_does_not_exists(): void
    {
        $this->expectException(CurrencyNotFoundException::class);

        $this->converter->from(new Price(500, 'USD'))->to('WWW')->convert();
    }

    public function test_can_convert_amount(): void
    {
        $this->converter->from(new Price(2, 'EUR'))->to('USD');

        $currency = new Currency('FCU', 2, 0);

        $this->assertSame(4.0, $this->converter->convertAmount($currency));

        $this->converter->from(new Price(4, 'USD'))->to('EUR');

        $this->assertSame(2.0, $this->converter->convertAmount($currency));
    }

    public function test_decimals_are_displayed_depending_on_the_currency(): void
    {
        $this->converter->from(new Price(2, 'EUR'));

        $currency = new Currency('FCU', 2.655, 2);

        $this->converter->to($currency);

        $this->assertSame(5.31, $this->converter->convertAmount($currency));

        $this->converter->from(new Price(4, 'USD'))->to('EUR');

        $this->assertSame(1.51, $this->converter->convertAmount($currency));

        $currency = new Currency('FCU', 2.655, 0);

        $this->converter->from(new Price(4, 'USD'));

        $this->assertSame(2.0, $this->converter->convertAmount($currency));
    }
}
