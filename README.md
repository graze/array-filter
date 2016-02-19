# array-filter

<img align="right" src="https://media2.giphy.com/media/l41lOm4da1Avr5ui4/giphy.gif" width="250px" />

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graze/array-access.svg?style=flat-square)](https://packagist.org/packages/graze/array-access)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/graze/array-access/master.svg?style=flat-square)](https://travis-ci.org/graze/array-access)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/graze/array-access.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/array-access/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/graze/array-access.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/array-access)
[![Total Downloads](https://img.shields.io/packagist/dt/graze/array-access.svg?style=flat-square)](https://packagist.org/packages/graze/array-access)

Array Filtering

## Install

Via Composer

```bash
$ composer require graze/array-filter
```

## Usage

### Filter Factory

There is a factory which takes in a set of string definitions and creates filters based on each one

```php
$config = [
    'name ~' => '/test.*/i',
    'ctime >' => '{date:yesterday:U}',
    'status in' => [1, 2],
];
$input = [[
    'name' => 'test1234',
    'ctime' => 142353782,
    'status' => 2,
]];
$factory = new FilterFactory(new ValueFactory());
$filter = $factory->createFilters($config);
$filtered = array_filter($input, $filter);
```

### Respect/Validator Compatible

```php
$filter = new AllOfFilter();
$filter->addFilter('name', v::regex('/test.*/i'))
       ->addFilter('ctime', v::date()->between('yesterday', 'today'))
       ->addFilter('status', v::in([1, 2]));

$filtered = array_filter($input, $filter);
```

## Testing

```bash
$ make
$ make test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email harry.bragg@graze.com instead of using the issue tracker.

## Credits

- [Harry Bragg](https://github.com/h-bragg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
