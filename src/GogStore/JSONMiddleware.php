<?php

namespace GogStore;

use MiladRahimi\PhpRouter\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

/**
 * This is a middleware needed to deal with JSON inputs in requests because PHPRouter library cannot do that on its own.
 * Author: Luca-Castelnuovo
 * Reference: https://github.com/miladrahimi/phprouter/issues/20
 *
 * @package GogStore
 */
class JSONMiddleware implements Middleware
{
    /**
     * If POST,PUT,PATCH requests contains JSON interpret it
     * Also validate that the provided JSON is valid.
     *
     * @param ServerRequestInterface $request
     * @param $next
     *
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, $next)
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {

            if (!isset($request->getHeaders()['content-type'])
                || $request->getHeaders()['content-type'][0] !== "application/json") {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Content-Type should be "application/json".'
                ], 400);
            }

            $data = json_decode($request->getBody()->getContents(), true);

            if ((JSON_ERROR_NONE !== json_last_error())) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Provided JSON input is invalid.'
                ], 400);
            }

            $request->data = $data;
        }

        return $next($request);
    }
}