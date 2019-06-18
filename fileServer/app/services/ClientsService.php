<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 20.09.18
 * Copyright (c): Hawai Project, 2018
 */

namespace App\Services;

use App\Models\Clients;
use App\Models\Tags;

class clientsService extends AbstractService
{
    /** Unable to get Clients */
    const ERROR_UNABLE_TO_FIND_CLIENTS = 11001;

    /** Unable to find tag */
    const ERROR_UNABLE_TO_FIND_TAG = 11002;

    /*
     * Todo client id kann die client id vom identity server sein.
     */

    /**
     * returns all found clients
     *
     * @return array
     */
    public function getClientsList()
    {
        try {
            // search for client in database
            $clients = Clients::find(
              [
                  'conditions'  => '',
                  'bind'        => [],
                  'columns'     => 'id, name, preview_pic, short_description, redirect'
              ]
            );

            if (!$clients) {
                return [];
            }

            return $clients->toArray();
        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * returns all clients with specifig tag
     *
     * @param array $clientsData
     * @return mixed
     */
    public function getClientsWithTag(array $clientsData)
    {
        try{
            // looking for client with tags in database
            $tag = Tags::findFirst(
                [
                    "name = :tag:",
                    "bind" => ["tag" => $clientsData['tag']],
                ]);

                $clients = $tag->clients;

            return $clients->toArray();
        } catch(\PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }
}