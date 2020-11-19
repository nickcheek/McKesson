# McKesson PHP Library

A simple library to send lookup, search, and order requests to McKesson.

## Installation

You can install the package via composer:

```bash
composer require nickcheek/mckesson
```

## Usage

### Item Search

```php
$search = new Mckesson($identity, $secret, $account_number, $b2b_key);
$result = $search->search('Tena Underwear') // or whatever you're searching
var_dump($result); //you can use $result->SearchResult also.
```

### Item Lookup

```php
$lookup = new Mckesson($identity, $secret, $account_number, $b2b_key);
$result = $lookup->lookup('978841') // or whatever you're searching
var_dump($result);
```

### Item Ordering

```php
//first you will need to setup the required arrays.
$customer = [
    'orderId' => 123, 
    'total' => '12.00', 
    'customerName' => 'Nicholas Cheek', 
    'address1' => '123 Anystreet Dr',
    'address2' => 'Suite F', //optional 
    'city' => 'Little Rock', 
    'state' => 'AR', 
    'zip' => 72019, 
    'phone' => '5011234567', //optional
    'email' => 'nick@nicholascheek.com' //optional (*i think*)
    'customerId' => 12345
    ];

$items = [
    'qty' => 2, 
    'sku' => 123456, 
    'price' => '12.00', 
    'uom' => 'BX'
    ];

$order = new Mckesson($identity, $secret, $account_number, $b2b_key);
$result = $order->order('978841') // or whatever you're searching
var_dump($result);
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email nick@nicholascheek.com instead of using the issue tracker.

## Credits

-   [Nicholas Cheek](https://github.com/nickcheek)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
