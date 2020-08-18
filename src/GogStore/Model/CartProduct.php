<?php

namespace GogStore\Model;

use GogStore\Database\Record\AbstractRecord;
use GogStore\Database\Record\NullRecord;
use GogStore\Database\Record\ValidateException;
use GogStore\Pattern\Registry;

class CartProduct extends AbstractRecord
{
    public function __construct(array $data = [])
    {
        parent::__construct('cart_products', $data);
        $this->setDbAdapter(Registry::get('db'));
    }

    public function validate(): void
    {
        $allowedFields = ['cart_id', 'product_id', 'amount'];
        $this->data->replace(array_intersect_key($this->getData(false), array_flip($allowedFields)));

        if (!isset($this->data['cart_id']) || (new Cart)->getById($this->data['cart_id']) instanceof NullRecord)
            throw new ValidateException('Field "cart_id" is invalid. It has to represent an existing cart and is required.');

        if (!isset($this->data['product_id']) || (new Product)->getById($this->data['product_id']) instanceof NullRecord)
            throw new ValidateException('Field "product_id" is invalid. It has to represent an existing product and is required.');

        if (!isset($this->data['amount'])
            || !is_numeric($this->data['amount'])
            || $this->data['amount'] < 0
            || $this->data['amount'] > 10
            || (int)$this->data['amount'] != $this->data['amount']
        )
            throw new ValidateException('Field "amount" is invalid. It has to be a positive integer and is required.');
    }
}