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
    'email' => 'nick@nicholascheek.com', //optional (*i think*)
    'customerId' => 12345
    ];

$item1 = [
    'qty' => 2,
    'sku' => 123456,
    'price' => '12.00',
    'uom' => 'BX'
];

$item2 = [
    'qty' => 2,
    'sku' => 78901,
    'price' => '12.00',
    'uom' => 'BX'
];

$order = [$item1, $item2];

$order = new Mckesson($identity, $secret, $account_number, $b2b_key);
$result = $order->order($customer, $order) // or whatever you're searching
var_dump($result);
```
### Using the Builder
```php
//You can use the builder to make it easy to build up items and customers.
$builder = new Mckesson($identity, $secret, $account_number, $b2b_key);
$items = $builder->addItem(2, '123456','12.00','BX');
$items = $builder->addItem(1, '23939291','23.00','EA');
$items = $builder->addItem(19, '8328237','83.00','CS');
$customer = $builder->addCustomer(12345,'23.00','nick cheek','201 MyStreet Ave', '', 'Little Rock', 'AR',72204,'','',123456778);

//afterwards, you can order like so:
$order = $builder->order($customer, $items);


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
