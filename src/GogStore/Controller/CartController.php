<?php

namespace GogStore\Controller;

use GogStore\Logic\CartFacade;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;

class CartController
{
    public function create(ServerRequest $request)
    {
        $cart = CartFacade::createNewCart();
        return new JsonResponse(['status' => 'ok', 'id' => $cart->getId()]);
    }

    public function get(string $cart_id, ServerRequest $request)
    {
        $cart = CartFacade::showCart($cart_id);
        return new JsonResponse($cart);
    }

    public function put(string $cart_id, string $product_id, ServerRequest $request)
    {
        $data = $request->data;
        CartFacade::addProductToCart($cart_id, $product_id, (int)($data['amount'] ?? 1));
        return new JsonResponse(['status' => 'ok']);
    }

    public function remove(string $cart_id, string $product_id, ServerRequest $request)
    {
        CartFacade::removeProductFromCart($cart_id, $product_id);
        return new JsonResponse(['status' => 'ok']);
    }
}