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
 * Class Http401Exception
 *
 * Execption class for Unauthorized Error (401)
 *
 * @package App\Lib\Exceptions
 */
class Http401Exception extends AbstractHttpException
{
    protected $httpCode = 401;
    protected $httpMessage = 'Unauthorized';
}