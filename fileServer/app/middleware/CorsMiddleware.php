<?php

namespace App\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * CORSMiddleware
 *
 * CORS checking
 */
class CorsMiddleware implements MiddlewareInterface
{
    /**
     * Before anything happens
     *
     * @param Event $event
     * @param Micro $app
     *
     * @returns bool
     */
    public function beforeHandleRoute(Event $event, Micro $app)
    {

         if ($app->request->getHeader('ORIGIN')) {
            $origin = $app->request->getHeader('ORIGIN');
        } else {
            $origin = '*';
        }

        $app
            ->response
            ->setHeader('Access-Control-Allow-Origin', $origin)
            ->setHeader(
                'Access-Control-Allow-Methods',
                'GET,PUT,POST,DELETE,OPTIONS'
            )
            ->setHeader(
                'Access-Control-Allow-Headers',
                'Origin, X-Requested-With, Content-Range, ' .
                'Content-Disposition, Content-Type, Authorization'
            )
           ->setHeader('Access-Control-Expose-Headers',
               'Origin, X-Requested-With, Content-Range, ' .
               'Disposition, Content-Type')
            ->setHeader('Access-Control-Allow-Credentials', 'true');

        $app->response->sendHeaders();
        $referenceToken = $app->request->getHeader('Authorization');

        if ($referenceToken != null)
            $app->session->set("auth-token", $referenceToken);

        return true;
    }

    /**
     * Calls the middleware
     *
     * @param Micro $app
     *
     * @returns bool
     */
    public function call(Micro $app)
    {
        return true;
    }
}