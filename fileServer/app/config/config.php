<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 16.04.18
 * Copyright (c): Hawai Project, 2018
 */

/**
 * set database and application configuration
 */
return new \Phalcon\Config(
    [
        'database' => [
            'adapter' => 'Mysql',
            'host' => 'database',
            'port' => '3306',
            'username' => 'admin',
            'password' => 'fileserverSecret',
            'dbname' => 'fileserver',
        ],

        'application' => [
            'controllersDir' => "app/controllers/",
            'modelsDir' => "app/models",
            'baseUri' => "/",
        ],
    ]
);