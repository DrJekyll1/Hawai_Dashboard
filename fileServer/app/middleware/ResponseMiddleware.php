<?php
namespace App\Middleware;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use App\Controllers\AbstractHttpException;

/**
 * ResponseMiddleware
 *
 * Manipulates the response
 */
class ResponseMiddleware implements MiddlewareInterface
{
    /**
     * After working with request send response
     *
     * @param Micro $app
     *
     * @returns bool
     */
    public function call(Micro $app)
    {
        try {

            $return = $app->getReturnedValue();

            if (is_array($return)) {
                // Transforming arrays to JSON
                $app->response->setContent(json_encode($return));
            } elseif (!strlen($return)) {
                // Successful response without any content
                $app->response->setStatusCode('204', 'No Content');
            } else {
                // Unexpected response
                throw new Exception('Bad Response');
            }
            $app->response->send();
            return true;

        } catch (AbstractHttpException $e) {
            $response = $app->response;
            $response->setStatusCode($e->getCode(), $e->getMessage());
            $response->setJsonContent($e->getAppError());
            $response->send();
        } catch (\Phalcon\Http\Request\Execption $e) {
            $app->response->setStatusCode(400, 'Bad request')
                ->setJsonContent([
                    AbstractHttpException::KEY_CODE => 400,
                    AbstractHttpException::KEY_MESSAGE => 'Bad request'
                ])
                ->send();
        } catch (\Exception $e) {
            if ($e->getCode() === null) {
                $app->response->setStatusCode(500, 'Internal Server Error');
            }

        }
    }
}