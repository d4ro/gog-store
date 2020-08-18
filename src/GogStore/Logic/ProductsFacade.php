<?php

namespace GogStore\Logic;

use GogStore\Database\Record\Record;
use GogStore\Database\Record\ValidateException;
use GogStore\Model\Cart;
use GogStore\Model\Product;
use GogStore\Pattern\Collection\Collection;

class ProductsFacade
{
    public static int $perPage = 3;

    public static function addProduct(array $data): Record
    {
        $product = new Product($data);
        if (isset($data['title'])) {
            $existing = $product->find(['title' => $data['title']]);
            if ($existing->count())
                throw new ValidateException('Another product has already this title.');
        }
        $product->insert();
        return $product;
    }

    public static function removeProduct(string $id): void
    {
        $tmp = new Product();
        $product = $tmp->getById($id);
        $product->remove();
    }

    public static function listProducts(int $page = 1): Collection
    {
        $tmp = new Product();
        return $tmp->find([], self::$perPage, ($page - 1) * self::$perPage);
    }

    public static function getProduct(string $id): Record
    {
        $tmp = new Product();
        return $tmp->getById($id);
    }

    public static function updateProduct(string $id, array $data): void
    {
        $product = self::getProduct($id);
        if (isset($data['title'])) {
            $existing = $product->find(['title' => $data['title']]);
            foreach ($existing as $record) {
                /**
                 * @var Record $record
                 */
                if ($record->getId() != $id)
                    throw new ValidateException('Another product has already this title.');
            }
        }

        $product->patch($data);
        $product->update();
    }
}