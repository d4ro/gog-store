<?php

namespace Tests\GogStore\Model;

use GogStore\Config;
use GogStore\Database\Adapter\MySQL;
use GogStore\Database\Record\Record;
use GogStore\Model\Product;
use GogStore\Pattern\Registry;
use PHPUnit\Framework\TestCase;

define('APP_PATH', realpath('../../../'));
Registry::set('db', new MySQL(Config::getInstance()));

class ProductTest extends TestCase
{
    public Product $product;

    protected function setUp(): void
    {
        $this->product = new Product();
    }

    public function testInstanceOf()
    {
        $this->assertTrue($this->product instanceof Record);
    }

    public function testSetGet()
    {
        $this->product->setValue('title', 'Some title');
        $this->assertEquals('Some title', $this->product->getValue('title'));
    }

    public function testValidate()
    {
        $this->product->setValue('title', 'Some title');
        $this->product->setValue('price', 1.23);
        $this->product->validate();
        $this->assertTrue(true);
    }

    public function testValidateFailsOnTitle()
    {
        try {
            $this->product->validate();
        } catch (\Exception $ex) {
            $this->assertEquals('Field "title" is invalid. It has to be a non empty text and is required.', $ex->getMessage());
        }
    }

    public function testValidateFailsOnPrice()
    {
        $this->expectExceptionMessage('Field "price" is invalid. It has to be a positive number and is required.');
        $this->product->setValue('title', 'Some title');
        $this->product->validate();
    }
}
