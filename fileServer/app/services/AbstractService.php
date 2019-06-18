<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 18.04.18
 * Copyright (c): Hawai Project, 2018
 */

namespace App\Services;

/**
 * Class AbstractService
 *
 * @property \Phalcon\Db\Adapter\Pdo\MysqlExtended $db
 * @property \Phalcon\Config                    $config
 */

abstract class AbstractService extends \Phalcon\DI\Injectable
{
    /*
     * Invalid parameters anywhere
     */
    const ERROR_INVALID_PARAMETERS = 10001;

    /*
     * Record already exists
     */
    const ERROR_ALREADY_EXISTS = 10002;

}