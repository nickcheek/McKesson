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
$result = $search->search('Tena Underwear');
var_dump($result);
```

##### You can also pass an additional search type of 'refinement' as the third search pararmeter. The result will display a list of categories where the product you're searching for can be found. If you're searching gloves, you'll get over 2000 results. If you're looking for gloves for physical theapy, you can narrow it down to 15 results using the refinement and node feature.

```php
$search = new Mckesson($identity, $secret, $account_number, $b2b_key);
$result = $search->lookup('978841',null, 'refinement');
var_dump($result);
```

##### To only search for physical therapy gloves, you should pass the node number as the fourth and final parameter. If you pass 'refinement' parameter again, it will further narrow your search nodes. So your search will look as such:

```php
$search = new Mckesson($identity, $secret, $account_number, $b2b_key);
$result = $search->lookup('978841', null, 'refinement', 16873);
var_dump($result);
```

##### The above search would have narrowed your 2000+ gloves down the 15 with the ability to narrow it down even further using the three addional nodes of Exercise Equipment, Treatments, and Self-Help Aids.

### Item Lookup

```php
$lookup = new Mckesson($identity, $secret, $account_number, $b2b_key);
$result = $lookup->lookup('978841');
var_dump($result);
```

### Types

#### You can also pass an additional type or combination of types with the above methods. They include availability, detail, extra, and image. If no type is chosen, it will default to 'availability detail'.

-   availability - Updating prices or checking availability.
-   detail - General product information.
-   extra - Ideal for product images, features, and atrributes.
-   image - To get the item image.

```php
$lookup = new Mckesson($identity, $secret, $account_number, $b2b_key);
$result = $lookup->lookup('978841','extra');
var_dump($result);
```

### Item Feed

```php
$feed = new Mckesson($identity, $secret, $account_number, $b2b_key);
$result = $feed->feed('foo','list', 802115, 'detail availability');
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
