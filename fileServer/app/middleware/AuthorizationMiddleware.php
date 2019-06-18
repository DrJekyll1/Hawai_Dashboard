<?php
namespace App\Middleware;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use GuzzleHttp\Client;
use App\Services\ServiceException;


/**
 * AuthorizationMiddleware
 *
 * Check the authentication of the requests
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    /**
     * Authorize the Request
     *
     * @param Event $event
     * @param Micro $app
     *
     * @returns bool
     */
    public function beforeExecuteRoute(Event $event, Micro $app)
    {

        try {
            $referenceToken = $app->session->get("auth-token");

            $client = new Client(['base_uri' => BASE_URL]);

            $uri = 'connect/introspect';

            //sending request to the OAuth-server to identify the client
            $response = $client->post( $uri, [
                    'auth'    => [
                        'FileServerApi',
                        'FileServerSecret'
                    ],

                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ],
                    'form_params' => [
                        'token' => $referenceToken
                    ],

                ]
            );

            //get the body of the response from OAuth-Server
            $raw_body = $response->getBody()->getContents();

            $body = json_decode($raw_body, true);

            if ($body['active'] == true) {
                $app->session->set("userId", $body['sub']);
                $app->session->set("client_id", $body['client_id']);
            }

            //if active false authentication is failed, else response the client request
            if (empty($body['active']) != null) {
                $exception =
                    new \App\Controllers\HttpExceptions\Http401Exception(
                        _('You have no permission to access'),
                        \App\Controllers\AbstractController::ERROR_AUTHENTICATION_FAILED,
                        new \Exception ('Unauthorized')
                    );
                throw $exception;
            }else{
                return true;
            }
        } catch (ServiceException $e) {

        }
    }

    /**
     * Calls the middleware
     *
     * @param Micro $application
     *
     * @returns bool
     */
    public function call(Micro $application)
    {

        return true;
    }
}