<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 24.09.18
 * Copyright (c): Hawai Project, 2018
 */

namespace App\Controllers;
use App\Controllers\HttpExceptions\Http400Exception;
use App\Controllers\HttpExceptions\Http422Exception;
use App\Controllers\HttpExceptions\Http500Exception;
use App\Services\AbstractService;
use App\Services\ServiceException;
use App\Services\ClientsService;


/**
 * Operations with clientsService
 */
class ClientsController extends AbstractController
{
    /**
     * Returns clientsService list
     *
     * @param
     * @return array
     */
    public function getClientsAction()
    {
        try {
            //call the clientService an get Clientlist
            $clientList = $this->clientsService->getClientsList();
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $clientList;
    }

    /**
     * Returns clientsService list with specific tag
     *
     * @param string $tag
     * @return array
     */
    public function getClientsWithTagAction($tag)
    {
        $errors = [];
        $data = [];

        // check if tag correcr
        if ((!is_string($tag)) && (!is_null($tag))) {
            $errors['tag'] = 'tag must be a string';
        }

        $data['tag'] = (string)$tag;
        // are there erros repsonse thse
        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'), self::ERROR_INVALID_REQUEST);
            throw $exception->addErrorDetails($errors);
        }

        try {
            //call the client service
            $clientsWithTag = $this->clientsService->getClientsWithTag($data);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case ClientsService::ERROR_UNABLE_TO_FIND_CLIENTS:
                case ClientsService::ERROR_UNABLE_TO_FIND_TAG:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }

        }
        return $clientsWithTag;
    }
}