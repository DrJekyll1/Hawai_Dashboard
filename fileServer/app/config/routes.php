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
 * defining URIs
 */
$clientsCollection = new \Phalcon\Mvc\Micro\Collection();
$clientsCollection->setHandler('\App\Controllers\ClientsController', true);
$clientsCollection->setPrefix('/client');
$clientsCollection->get('/list', 'getClientsAction');
$clientsCollection->get('/tag/{tag:[a-z]+}', 'getClientsWithTagAction');
$app->mount($clientsCollection);

$filesCollection = new \Phalcon\Mvc\Micro\Collection();
$filesCollection->setHandler('\App\Controllers\FilesController', true);
$filesCollection->setPrefix('/file');
$filesCollection->post('/add/{tag:[a-z]+}', 'addAction');

$filesCollection->get('/download/{name}/{version}/{year:([0-9]{8})}', 'downloadAction');

$filesCollection->post('/delete/{tag:[a-z]+}', 'deleteAction');

$filesCollection->get('/list', 'getFilesAction');
$filesCollection->get('/list/{version:[0-9]+}', 'getFilesVersionAction');
$filesCollection->get('/list/{year:([0-9]{4})}/{month:([0-9]{2})}/{day:([0-9]{2})}','getFilesDateAction');
$filesCollection->get('/list/{tag:[a-z]+}', 'getFilesTagAction');

$app->mount($filesCollection);

// not found URLs
$app->notFound(
    function () use ($app) {
        $exception =
            new \App\Controllers\HttpExceptions\Http404Exception(
                _('URI not found or error in request.'),
                \App\Controllers\AbstractController::ERROR_NOT_FOUND,
                new \Exception('URI not found: ' . $app->request->getMethod() . ' ' . $app->request->getURI())
            );
        throw $exception;
    }
);
