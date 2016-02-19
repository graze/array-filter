# array-filter

<img align="right" src="https://media2.giphy.com/media/l41lOm4da1Avr5ui4/giphy.gif" width="250px" />

File manipulation

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
