# Convert price amounts between currencies

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gnahotelsolutions/currency-converter.svg?style=flat-square)](https://packagist.org/packages/gnahotelsolutions/currency-converter)
[![Build Status](https://img.shields.io/travis/gnahotelsolutions/currency-converter/master.svg?style=flat-square)](https://travis-ci.org/gnahotelsolutions/currency-converter)
[![Quality Score](https://img.shields.io/scrutinizer/g/gnahotelsolutions/currency-converter.svg?style=flat-square)](https://scrutinizer-ci.com/g/gnahotelsolutions/currency-converter)
[![Total Downloads](https://img.shields.io/packagist/dt/gnahotelsolutions/currency-converter.svg?style=flat-square)](https://packagist.org/packages/gnahotelsolutions/currency-converter)

Use this package to convert prices from one currency to another by the exchange rate using a base currency.

## Installation

You can install the package via composer:

```bash
composer require gnahotelsolutions/currency-converter
```

## Usage

Before anything, you need to create a repository of currencies to be loaded into the `Converter`.

A currency needs a `name`, `exchange rate` and `decimals` to be shown after the conversion.

```php
$repository = new CurrenciesRepository([
    new Currency('EUR', 1, 2),
    new Currency('USD', 1.1, 2),
    // ...
]);
```

When declaring a `Converter`, you'll also have to tell what's your base currency from where the exchange rates are calculated.
``` php
$baseCurrency = new Currency('EUR', 1, 2);

$converter = new Converter($baseCurrency, $repository);
```

Once your `Converter` is ready, you can transform a `Price` using the fluent interface. The result will be a new `Price` instance with the converted amount and the desired currency

```php
$price = new Price(1000, 'EUR');

$converter->from($price)->to('USD')->convert(); // new Price(1100, 'USD')
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email dllop@gnahs.com instead of using the issue tracker.

## Credits

- [GNA Hotel Solutions](https://github.com/gnahotelsolutions)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.