<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 16.04.18
 * Copyright (c): Hawai Project, 2018
 */

$loader = new \Phalcon\Loader();

/**
 * register all namespaces
 */
$loader->registerNamespaces(
    [
        'App\Services' => realpath(__DIR__ . '/../services/'),
        'App\Controllers' => realpath(__DIR__ . '/../controllers/'),
        'App\Models' => realpath(__DIR__ . '/../models'),
        'App\Middleware' => realpath(__DIR__ . '/../middleware'),
    ]
);

$loader->register();