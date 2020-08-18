<?php

namespace GogStore\Model;

use GogStore\Database\Record\AbstractRecord;
use GogStore\Pattern\Registry;

class Cart extends AbstractRecord
{
    public function __construct(array $data = [])
    {
        parent::__construct('carts', $data);
        $this->setDbAdapter(Registry::get('db'));
    }

    public function validate(): void
    {
        // cart has no data on its own
        $this->data->clear();
    }
}