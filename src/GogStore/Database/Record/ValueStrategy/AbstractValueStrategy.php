<?php

namespace GogStore\Database\Record\ValueStrategy;

use GogStore\Pattern\DataWrapper;

abstract class AbstractValueStrategy
{
    protected DataWrapper $data;

    public function __construct(DataWrapper $data)
    {
        $this->data = $data;
    }
}