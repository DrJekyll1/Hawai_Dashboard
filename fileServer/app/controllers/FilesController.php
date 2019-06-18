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
use Phalcon\Http\Request;
use App\Services\FilesService;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File;


/**
 * Operations with Files: CRUD
 */

class FilesController extends AbstractController
{
    /***
     *
     * upload a file to the users store
     *
     */
    public function addAction($tag)
    {
        $errors = [];
        $data = [];
        $success = [];

        $data['tag'] = $tag;

        //check if tag correct
        if (!empty($data['tag']) && (!is_string($data['tag']))){
            $errors['tag'] = 'String expected';

        }
        //are there errors response them
        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'), self::ERROR_INVALID_REQUEST);
            throw $exception->addErrorDetails($errors);
        }

        try {


            if ($this->request->hasFiles()){
                foreach ($this->request->getUploadedFiles() as $file){
                    $success['name'] = $file->getName() . ' uploaded';
                    //have the files the correct extension
                    if ($this->extensionCheck($file->getRealType())){
                        //call FileService and the upload function
                        $this->filesService->uploadFiles($data, $file);

                    }else {
                        $errors['file'] = 'This typ of file is not supported';
                    }
                }
                return $success;
            }
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case AbstractService::ERROR_ALREADY_EXISTS:
                case FilesService::ERROR_UNABLE_CREATE_FILE:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }
        }

    }

    /***
     * download a file from the user
     *
     * @param $name
     * @param $version
     * @param string $mode
     * @return mixed
     *
     */
    public function downloadAction($name, $version, $date, $mode = 'attachment'){

        $errors = [];
        $data = [];

        /*
         * check if the input params correct
         */

        if ($name != null) {
            if ((!is_string($name)) && (!is_null($name)))
                $errors['name'] = 'tag must be a string';
            $data['name'] = $name;
        }

        if ($version != null) {
            if (!ctype_digit($version) || ($version < 0))
                $errors['version'] = 'The version must be a positive Integer';
            $data['version'] = $version;
        }

        if ($date != null) {
            if (!ctype_digit($date) || ((strlen($date) < 0) || (strlen($date) > 8)))
                $errors['date'] = 'Date must be the format: YYYYMMDD';
            $data['date'] = $date;
        }
        // if there an error response it
        if ($errors) {
        $exception = new Http400Exception(_('Input parameters validation error'), self::ERROR_INVALID_REQUEST);
        throw $exception->addErrorDetails($errors);
        }

        try {
            //call Fileservicec for download a file
            $this->filesService->downloadFiles($data, $mode);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case FilesService::ERROR_UNABLE_TO_FIND_DOWNLOAD:
                case FilesService::ERROR_UNABLE_TO_OPEN_FILE:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }
        }
    }

    /***
     *
     * deletes a users file
     *
     * @param $tag
     */
    public function deleteAction($tag) {
        $errors =[];
        $data = [];

        /*
         * check if the the input params correct
         */
        $data['tag'] = $tag;
        $rawBody = $this->request->getJsonRawBody(true);

        if($rawBody != null) {
            foreach ($rawBody as $key => $value) {
                if ("{$key}" == 'filename')
                    $data['file_name'] = $value;
                elseif ("{$key}" == 'version')
                    $data['version'] = $value;
                elseif ("{$key}" == 'date')
                    $data['date'] = $value;
            }
        }

        if ($data['date'] != null) {
            if ((!is_null($data['tag'])) && (!is_string($data['tag'])))
                $errors['tag'] = 'String expected';
        }

        if ($data['file_name'] != null) {
            if ($data['file_name'] != null) {
                if (!is_string($data['file_name']))
                    $errors['file_name'] = 'String expected';
            }

        }

       if ($data['version'] != null) {
            if ($data['version'] != null) {
                if (!ctype_digit($data['version']) || ($data['version'] < 0))
                    $errors['version'] = 'The version must be a positive Integer';
            }
        }

       if ($data['date'] != null) {
            if ($data['date'] != null) {
                if (!ctype_digit($data['date']) || ((strlen($data['date']) < 0) || (strlen($data['date']) > 8)))
                    $errors['date'] = 'Date must be the format: YYYYMMDD';
            }
       }

        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'), self::ERROR_INVALID_REQUEST);
            throw $exception->addErrorDetails($errors);
        }

        try {
            //Service call to delete a file
            $this->filesService->deleteFile($data);

        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case AbstractService::ERROR_ALREADY_EXISTS:
                case FilesService::ERROR_UNABLE_CREATE_FILE:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }
        }
        $success['status'] = 'file deleted';
        return $success;
    }

    /***
     *
     * get a list of all users files
     *
     * @return mixed
     */
    public function getFilesAction() {

        try {
            // service call to get a list of files
            $fileList = $this->filesService->getFileList();
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case FilesService::ERROR_UNABLE_TO_FIND_FILES:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }
        }

        if (!empty($fileList))
            return $fileList;
    }

    /***
     *
     * get a list of users files of a specific version
     *
     * @param $version
     * @return mixed
     */
    public function getFilesVersionAction($version) {

        $errors = [];
        $data = [];

        /*
        * check if the the input params correct
        */
        $data['version'] = $version;

        if ($data['version'] != null) {
            if (!ctype_digit($data['version']) || ($data['version'] < 0))
                    $errors['version'] = 'The version must be a positive Integer';
        }
        // if there errors response them
        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'), self::ERROR_INVALID_REQUEST);
            throw $exception->addErrorDetails($errors);
        }

        try {
            // service call to get a specific list of files
            $fileList = $this->filesService->getSpecificFileList($data);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case FilesService::ERROR_UNABLE_TO_FIND_FILES:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }
        }
        return $fileList;
    }

    /***
     *
     * get a list of users files from a specific day
     *
     * @param $year
     * @param $month
     * @param $day
     * @return mixed
     */
    public function getFilesDateAction($year, $month, $day) {

        $errors = [];
        $data = [];

        /*
        * check if the the input params correct
        */
        $data['date'] = $year.$month.$day;
        if ($data['date'] != null) {
            if (!ctype_digit($data['date']) || ((strlen($data['date']) < 0) || (strlen($data['date']) > 8)))
                $errors['date'] = 'Date must be the format: YYYYMMDD';
        }

        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'), self::ERROR_INVALID_REQUEST);
            throw $exception->addErrorDetails($errors);
        }

        try {
            // service call to get specific list of files
            $fileList = $this->filesService->getSpecificFileList($data);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case FilesService::ERROR_UNABLE_TO_FIND_FILES:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }
        }
        return $fileList;
    }

    /***
     * get a list of users files from a specific client tag
     *
     * @param $tag
     * @return mixed
     */
    public function getFilesTagAction($tag) {

        $errors = [];
        $data = [];

        /*
        * check if the the input params correct
        */
        $data['tag'] = $tag;
        if ((!is_null($tag)) && (!is_string($tag)))
            $errors['tag'] = 'String expected';

        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'), self::ERROR_INVALID_REQUEST);
            throw $exception->addErrorDetails($errors);
        }

        try {
            // service call to get specific list of files
            $fileList = $this->filesService->getSpecificFileList($data);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case FilesService::ERROR_UNABLE_TO_FIND_FILES:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }

        }
        return $fileList;
    }

    /**
     * helper function to check if the file have the correct extension
     *
     * @param $extension
     * @return bool
     */

    private function extensionCheck($extension)
    {
        $allowedTypes = [
           'text/plain',
           'text/html',
           'application/json',
           'application/xml',
        ];
        return in_array($extension, $allowedTypes);
    }
}