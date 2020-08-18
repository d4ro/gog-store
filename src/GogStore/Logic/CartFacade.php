<?php

namespace GogStore\Logic;

use GogStore\Database\Record\Record;
use GogStore\Database\Record\ValidateException;
use GogStore\Model\Cart;
use GogStore\Model\CartProduct;

class CartFacade
{
    public static function createNewCart(): Record
    {
        $cart = new Cart();
        $cart->insert();
        return $cart;
    }

    public static function showCart(string $id): Record
    {
        $tmp = new Cart();
        $cart = $tmp->getById($id);

        $tmp = new CartProduct();
        $products = $tmp->findJoin([[
            'schema' => 'products',
            'type' => 'INNER',
            'local' => 'product_id',
            'remote' => 'id',
        ]], ['cart_products.id', 'product_id', 'title', 'price', 'amount'],
            ['cart_id' => $cart->getId()]);

        $cart->setValue('products', $products->toArray());
        $cart->setValue('total', array_reduce($products->toArray(), function ($sum, Record $record) {
            return $sum + $record->getValue('price') * $record->getValue('amount');
        }, 0));

        return $cart;
    }

    public static function addProductToCart(string $cartId, string $productId, int $amount): void
    {
        $data = ['cart_id' => $cartId, 'product_id' => $productId];

        // get or create cart product
        $cartProduct = new CartProduct($data);
        $foundCartProduct = $cartProduct->find($data);
        if ($foundCartProduct->count())
            $cartProduct = $foundCartProduct[0];

        // check how many products we have in a cart
        $foundCartProducts = $cartProduct->find(['cart_id' => $cartId]);
        $amountOfProductsInCart = $foundCartProducts->count() - $foundCartProduct->count() + 1;
        if ($amountOfProductsInCart > 3)
            throw new ValidateException("You cannot add to a cart more than 3 products.");

        // check how many copies of this product we have in a cart
        $currentAmount = $cartProduct->getValue('amount', 0);
        $cartProduct->setValue('amount', $currentAmount + $amount);
        if ($cartProduct->getValue('amount') > 10)
            throw new ValidateException("You cannot add to a cart more than 10 items of the same type. " .
                "You tried to add $amount when in a cart was already $currentAmount");

        // finally save a record
        $cartProduct->save();
    }

    public static function removeProductFromCart(string $cartId, string $productId): void
    {
        $data = ['cart_id' => $cartId, 'product_id' => $productId];

        $tmp = new CartProduct();
        $tmp->delete($data);
    }
}