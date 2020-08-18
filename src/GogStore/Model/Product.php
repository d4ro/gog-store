<?php

namespace GogStore\Model;

use GogStore\Database\Record\AbstractRecord;
use GogStore\Database\Record\ValidateException;
use GogStore\Pattern\Registry;

class Product extends AbstractRecord
{
    public function __construct(array $data = [])
    {
        parent::__construct('products', $data);
        $this->setDbAdapter(Registry::get('db'));
    }

    /**
     * Throws an exception if a product price is not numeric or is negative.
     *
     * @throws ValidateException
     */
    public function validate(): void
    {
        $allowedFields = ['title', 'price'];
        $this->data->replace(array_intersect_key($this->getData(false), array_flip($allowedFields)));

        if (!isset($this->data['title']) || empty($this->data['title']))
            throw new ValidateException('Field "title" is invalid. It has to be a non empty text and is required.');

        if (!isset($this->data['price']) || !is_numeric($this->data['price']) || $this->data['price'] < 0)
            throw new ValidateException('Field "price" is invalid. It has to be a positive number and is required.');

    }
}