<?php

namespace Tests;

use GNAHotelSolutions\CurrencyConverter\Converter;
use GNAHotelSolutions\CurrencyConverter\CurrenciesRepository;
use GNAHotelSolutions\CurrencyConverter\Currency;
use GNAHotelSolutions\CurrencyConverter\Exceptions\CurrencyNotFoundException;
use GNAHotelSolutions\CurrencyConverter\Price;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{

    /** @var  Converter */
    private $converter;

    protected function setUp()
    {
        parent::setUp();

        $baseCurrency = new Currency('EUR', 1, 2);

        $this->repository = new CurrenciesRepository([
            $baseCurrency,
            new Currency('USD', 0.5, 2),
            new Currency('GBP', 2, 2)
        ]);

        $this->converter = new Converter($baseCurrency, $this->repository);
    }

    /** @test */
    public function from_price_is_assigned()
    {
        $price = new Price(1000, 'EUR');

        $this->converter->from($price);

        $this->assertEquals($price, $this->converter->price());
    }

    /** @test */
    public function to_currency_is_assigned()
    {
        $this->converter->to('USD');

        $this->asserttrue($this->converter->currency()->is('USD'));
    }

    /** @test */
    public function can_convert_currency_to_base()
    {
        $priceEuro = $this->converter->from(new Price(500, 'USD'))->convertToBase();

        $this->assertEquals('EUR', $priceEuro->currency());

        $this->assertEquals(1000, $priceEuro->amount());

        $price = $this->converter->from(new Price(500, 'GBP'))->convertToBase();

        $this->assertEquals(250, $price->amount());

        $this->converter = new Converter(new Currency('FCU', 10, 2), $this->repository);

        $priceBase = $this->converter->from(new Price(500, 'USD'))->convertToBase();

        $this->assertEquals('FCU', $priceBase->currency());

        $this->assertEquals(10000, $priceBase->amount());

        $price = $this->converter->from(new Price(500, 'GBP'))->convertToBase();

        $this->assertEquals(2500, $price->amount());
    }

    /** @test */
    public function currency_is_converted()
    {
        $price = $this->converter->from(new Price(500, 'USD'))->to('GBP')->convert();

        $this->assertNotEquals(500, $price->amount());

        $this->assertEquals('GBP', $price->currency());

        $price = $this->converter->from(new Price(500, 'USD'))->to('USD')->convert();

        $this->assertEquals(500, $price->amount());

        $this->assertEquals('USD', $price->currency());

        $price = $this->converter->from(new Price(500, 'USD'))->to('EUR')->convert();

        $this->assertEquals(1000, $price->amount());

        $this->assertEquals('EUR', $price->currency());
    }

    /** @test */
    public function exception_is_thrown_if_currency_does_not_exists()
    {
        $this->expectException(CurrencyNotFoundException::class);
        
        $price = $this->converter->from(new Price(500, 'USD'))->to('WWW')->convert();
    }

    /** @test */
    public function can_convert_amount()
    {
        $this->converter->from(new Price(2, 'EUR'))->to('USD');

        $currency = new Currency('FCU', 2, 0);

        $this->assertEquals(4, $this->converter->convertAmount($currency));

        $this->converter->from(new Price(4, 'USD'))->to('EUR');

        $this->assertEquals(2, $this->converter->convertAmount($currency));
    }

    /** @test */
    public function decimals_are_displayed_depending_on_the_currency()
    {
        $this->converter->from(new Price(2, 'EUR'));

        $currency = new Currency('FCU', 2.655, 2);

        $this->assertEquals(5.31, $this->converter->convertAmount($currency));

        $this->converter->from(new Price(4, 'USD'))->to('EUR');

        $this->assertEquals(1.51, $this->converter->convertAmount($currency));

        $currency = new Currency('FCU', 2.655, 0);

        $this->converter->from(new Price(4, 'USD'));

        $this->assertEquals(2, $this->converter->convertAmount($currency));

    }
}
