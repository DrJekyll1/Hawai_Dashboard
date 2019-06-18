<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 16.04.18
 * Copyright (c): Hawai Project, 2018
 */

namespace App\Controllers\HttpExceptions;

use App\Controllers\AbstractHttpException;

/**
 * Class Http422Exception
 *
 * Execption class for Unprocessable entity Error (422)
 *
 * @package App\Lib\Exceptions
 */
class Http422Exception extends AbstractHttpException
{
    protected $httpCode = 422;
    protected $httpMessage = 'Unprocessable entity';
}
