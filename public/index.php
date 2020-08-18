<?php

use GogStore\Config;
use GogStore\Controller\CartController;
use GogStore\Controller\ProductsController;
use GogStore\Database\Adapter\AbstractAdapter;
use GogStore\Database\Record\ValidateException;
use GogStore\JSONMiddleware;
use GogStore\Log;
use GogStore\Pattern\Registry;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use MiladRahimi\PhpRouter\Router;
use Zend\Diactoros\Response\JsonResponse;

define('APP_PATH', realpath('../'));

// main autoloader
require '../vendor/autoload.php';

Log::getInstance()->info('Handling API request', 'New API request');

// config as a singleton
$config = Config::getInstance();

// adapter factory method
$db = AbstractAdapter::createAdapter($config);
Registry::set('db', $db);

// define routing
$router = new Router();

// route!
// may the will of heaven fulfill, one must always agree with it
try {
    $router->group(['middleware' => JSONMiddleware::class], function (Router $router) {
        $router
            ->post('/products', ProductsController::class . '@create')
            ->get('/products', ProductsController::class . '@find')
            ->get('/products/{id}', ProductsController::class . '@get')
            ->put('/products/{id}', ProductsController::class . '@update')
            ->delete('/products/{id}', ProductsController::class . '@remove')
            //
            ->post('/cart', CartController::class . '@create')
            ->get('/cart/{cart_id}', CartController::class . '@get')
            ->put('/cart/{cart_id}/product/{product_id}', CartController::class . '@put')
            ->delete('/cart/{cart_id}/product/{product_id}', CartController::class . '@remove')
        ;
    })->dispatch();
} catch (RouteNotFoundException $e) {
    Log::getInstance()->warn('Router', 'Route is not found.');
    $router->getPublisher()->publish(new JsonResponse(['status' => 'notice', 'message' => 'Not found.'], 404));
} catch (ValidateException $e) {
    Log::getInstance()->warn('Validation', 'Model or input is not valid: ' . $e->getMessage());
    $router->getPublisher()->publish(new JsonResponse(['status' => 'invalid', 'message' => $e->getMessage()], 406));
} catch (Throwable $e) {
    Log::getInstance()->error('Handling API request', $e->getMessage(), $e);
    $router->getPublisher()->publish(new JsonResponse(['status' => 'error', 'message' => 'Internal error.'], 500));
}