<?php

namespace Nickcheek\McKesson;

class Builder
{

    protected iterable $items;

    public function addItem(int $qty, string $sku, string $price, string $uom): iterable
    {
        if (empty($this->items)) {
            $this->items = [
                'qty' => $qty,
                'sku' => $sku,
                'price' => $price,
                'uom' => $uom
            ];
        } else {
            $newArray = [
                'qty' => $qty,
                'sku' => $sku,
                'price' => $price,
                'uom' => $uom
            ];
            $this->items =  [$this->items, $newArray];
        }

        return $this->items;
    }

    public function addCustomer(int $orderId, string $total, string $customerName, string $address1, string $address2 = '', string $city, string $state, int $zip, string $phone = '', string $email = '', int $customerId): iterable
    {
        $customer = [
            'orderId' => $orderId,
            'total' => $total,
            'customerName' => $customerName,
            'address1' => $address1,
            'address2' => $address2,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'phone' => $phone, 
            'email' => $email, 
            'customerId' => $customerId
        ];
        return $customer;
    }
}
