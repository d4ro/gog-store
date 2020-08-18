<?php

namespace GogStore\Controller;

use GogStore\Logic\ProductsFacade;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;

class ProductsController
{
    public function create(ServerRequest $request)
    {
        $product = ProductsFacade::addProduct($request->data);
        return new JsonResponse(['status' => 'ok', 'id' => $product->getId()]);
    }

    public function get(string $id, ServerRequest $request)
    {
        $product = ProductsFacade::getProduct($id);
        return new JsonResponse($product->getData(true));
    }

    public function find(ServerRequest $request)
    {
        $params = $request->getQueryParams();
        if (isset($params['page']))
            $products = ProductsFacade::listProducts((int)$params['page']);
        else
            $products = ProductsFacade::listProducts();

        return new JsonResponse($products->toArray());
    }

    public function update(string $id, ServerRequest $request)
    {
        ProductsFacade::updateProduct($id, $request->data);
        return new JsonResponse(['status' => 'ok']);
    }

    public function remove(string $id, ServerRequest $request)
    {
        ProductsFacade::removeProduct($id);
        return new JsonResponse(['status' => 'ok']);
    }
}