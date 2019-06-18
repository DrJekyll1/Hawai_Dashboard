<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 28.08.18
 * Copyright (c): Hawai Project, 2018
 */

use App\Controllers\AbstractHttpException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Stream;
use Phalcon\Mvc\Micro;
use App\Controllers\HttpExceptions\Http401Exception;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Events\Manager;
use App\Middleware\CorsMiddleware;
use App\Middleware\ResponseMiddleware;
use App\Middleware\AuthorizationMiddleware;

try {

    $eventsManager = new Manager();


    // Loading Configs
    $config = require(__DIR__ . '/../app/config/config.php');

    //Autoloading classes
    require __DIR__ . '/../app/config/loader.php';

    // Initializing DI container
    $di = require __DIR__ . '/../app/config/di.php';

    // Initializing application
    $app = new Micro();

    // Autoload vendors
    require __DIR__ . '/../vendor/autoload.php';

    // Setting DI container
    $app->setDI($di);

    // Setting rounting
    require __DIR__. '/../app/config/routes.php';


    // defines the BASE URI to the Identity Server
    defined('BASE_URL')
        || define('BASE_URL', 'http://identityServer:80');

    // define path to application directory

    defined('ROOT_PATH')
    || define('ROOT_PATH' , realpath(dirname(__FILE__)).'/../');

    // define path to stored files directory

    defined('FILES_PATH')
    || define('FILES_PATH' , ROOT_PATH.'stored/userFiles/');

    // load CorsMiddleware
    $eventsManager->attach('micro', new CorsMiddleware());
    $app->before(new CorsMiddleware());


    // define response for Cors
    $app->options('/{catch:(.*)}', function() use ($app) {
    $app->response->setStatusCode(200, "OK")->send();
    });

    //define AuthorizationMiddleware
    $eventsManager->attach('micro', new AuthorizationMiddleware());
    $app->before(new AuthorizationMiddleware());
    
    //define ResponseMiddleware
    $eventsManager->attach('micro', new ResponseMiddleware());
    $app->after(new ResponseMiddleware());

    // set EventManager for Middleware
    $app->setEventsManager($eventsManager);
    // Processing request
    $app->handle();


} catch (AbstractHttpException $e) {
    $response = $app->response;
    $response->setStatusCode($e->getCode(), $e->getMessage());
    $response->setJsonContent($e->getAppError());
    $response->send();
} catch (\Phalcon\Http\Request\Execption $e) {
    $app->response->setStatusCode(400, 'Bad request')
                  ->setJsonContent([
                      AbstractHttpException::KEY_CODE =>400,
                      AbstractHttpException::KEY_MESSAGE => 'Bad request'
                  ])
        ->send();
} catch (\Exception $e) {
    if ($e->getCode() === null) {
        $app->response->setStatusCode(500, 'Internal Server Error');
    }
}


