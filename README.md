# array-filter

<img align="right" src="https://media2.giphy.com/media/l41lOm4da1Avr5ui4/giphy.gif" width="250px" />

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graze/array-filter.svg?style=flat-square)](https://packagist.org/packages/graze/array-filter)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/graze/array-filter/master.svg?style=flat-square)](https://travis-ci.org/graze/array-filter)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/graze/array-filter.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/array-filter/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/graze/array-filter.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/array-filter)
[![Total Downloads](https://img.shields.io/packagist/dt/graze/array-filter.svg?style=flat-square)](https://packagist.org/packages/graze/array-filter)

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

### Respect/Validator and other callable functions Compatible

```
$filter = new AllOfFilter();
$filter->addFilter(new ClosureFilter('name', v::regex('/test.*/i')))
       ->addFilter(v::key('ctime', v::date()->between('yesterday', 'today'))
       ->addFilter(function (array $data) {
           return isset($data['status']) && in_array($data['status'], [1, 2]);
       });

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

If you discover any security related issues, please email security@graze.com instead of using the issue tracker.

## Credits

- [Harry Bragg](https://github.com/h-bragg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
