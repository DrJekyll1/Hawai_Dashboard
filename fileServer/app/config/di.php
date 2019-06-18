<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 16.04.18
 * Copyright (c): Hawai Project, 2018
 */

use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as LogFileAdapter;

// Initializing a DI Container
$di = new \Phalcon\Di\FactoryDefault();

// use Phalcon\Di;
use Phalcon\Mvc\Model\Manager as ModelsManager;

$di->set(
    "modelsManager",
    function() {
        return new ModelsManager();
    }
);

/**
 * Overriding Response-object to set the Content-type header globally
 */
$di->setShared(
    'response',
    function () {
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'utf-8');

        return $response;
    }
);


/** common config */
$di->setShared('config', $config);

/**
 * Start the session the first time when some component request the session service
 */
$di->setShared(
    "session",
    function () {
        $session = new Session();

        $session->start();

        return $session;
    }
);

/**
 * Database
 */
$di->set(
    'db',
    function () use ($config) {
        return new PdoMysql(
            [
                "host" => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->dbname,
            ]
        );
    }
);

/**
 * Services to perform operations with the clients and the files
 */
$di->setShared('clientsService', '\App\Services\ClientsService');
$di->setShared('filesService', '\App\Services\FilesService');

return $di;