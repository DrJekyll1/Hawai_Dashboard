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

use Phalcon\Mvc\Model;

use Phalcon\Security\Random;
use App\Models\Files;
use App\Models\Tags;
use App\Models\Clients;
use App\Models\ClientsTags;
use App\Models\TagsFiles;
use App\Models\User;
use phpDocumentor\Reflection\File;
use Phalcon\Mvc\Model\Resultset;

class FilesService extends AbstractService
{
    const ERROR_UNABLE_CREATE_FILE = 12001;

    const ERROR_UNABLE_TO_FIND_DOWNLOAD = 12002;

    const ERROR_UNABLE_TO_OPEN_FILE = 12003;

    const ERROR_UNABLE_TO_FIND_FILES = 12004;

    /**
     * Uploads a file and make the correct db entries
     *
     * @param $data
     * @param $uploads
     */
    public function uploadFiles(array $data, $uploads) {
        try {
                $random = new Random();
                $userId = $this->session->get("userId");

                $data['fileId'] = $random->Uuid();
                $data['tagsFilesId'] = $random->Uuid();

                $data['current_Date'] = new \DateTime();

                $file_name = explode('.', $uploads->getName());

                // find the highest Version
                $version = Files::maximum(array(
                    'column' => 'version',
                    'conditions' => 'name = :name: AND date = :date: AND userId = :userId: AND extension = :extension:',
                    'bind'        => [
                        'name'    => $file_name[0],
                        'date'     => ($data['current_Date']->format('Y-m-d')),
                        'userId' => $userId,
                        'extension' => $uploads->getExtension()
                    ]
                ));

                // check if there is a file with the same name
                $existing_entries = Files::findFirst(
                    [
                        'conditions'  => 'name = :name: AND date = :date: AND version = :version:',
                        'order' => 'version',
                        'bind'        => [
                            'name'    => $file_name[0],
                            'date' => ($data['current_Date']->format('Y-m-d')),
                            'version' => $version
                        ]
                    ]
                );

                // if no entry was found
                if (!$existing_entries) {

                    $data['version'] = 1;

                    // check if there is already a client with that tag
                    $data['tag_id'] = $this->checkTagId($data['tag']);

                    // call data upload funtion
                    $result = $this->dataUpload($data, $uploads);

                } else {

                    // if there a file with the same name check date
                    if (($data['current_Date']->format('Y-m-d')) > $existing_entries->date) {
                        $data['version'] = 1;

                    } else {

                        // if there file with same name and date increase version
                        $data['version'] = $existing_entries->version;
                        $data['version'] ++;
                    }
                    // get tag_id
                    $data['tag_id'] = $this->checkTagId($data['tag']);
                    // call data upload function
                    $result = $this->dataUpload($data, $uploads);

                }
            if (!$result) {
                throw new ServiceException('Unable to create file', self::ERROR_UNABLE_CREATE_FILE);
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 1086) {
                throw new ServiceException('File already exists', self::ERROR_ALREADY_EXISTS);
            } else {
                throw new ServiceException($e->getMessage(). $e->getCode(), $e);            }
        }catch (\Exception $e){
            $this->response->setStatusCode(500, 'Internal Server Error');
        }
    }

    /**
     * @param $data
     * @param $mode
     */
    public function downloadFiles(array $data, $mode){

        try {
            $search = [];
            $single = explode(".", $data['name']);
            $search['name'] = $single[0];
            $search['extension'] = $single[1];
            $search['version'] = $data['version'];
            $search['date'] = $data['date'];
            $userId = $this->session->get("userId");
            $dir = FILES_PATH.$userId.'/';

            // find file in database
            $files = Files::findFirst([
                'conditions' => 'name=:name: AND extension=:extension: AND version=:version: AND date = :date:',
                'bind' => [
                    'name' => $search['name'],
                    'extension' => $search['extension'],
                    'version' => $search['version'],
                    'date' => $search['date']
                ]
            ]);

            if ($files == null) {
                throw new ServiceException('Can not find file', self::ERROR_UNABLE_TO_FIND_FILES);
            }

            $file_name = $files->name;
            $file_ext = $files->extension;
            $file_path  = $dir.$files->id.'_'.$file_name.'.'.$file_ext;


            if (is_file($file_path)){
                $file_size = filesize($file_path);
                $file = @fopen($file_path, 'rb');
                if($file) {
                    //set headers, prevent chaching
                    header('Pragma: public');
                   header('Expires: -1');

                    header('Cache-Control: public, must-revalidate, post-check=0, pre-check=0');
                    //set appropriate headers for attachment or streamed file
                    if ($mode == 'attachment') {
                        //     echo 'header attachment';
                        header("Disposition: attachment; filename=\"{$file_name}.{$file_ext}\" ");
                    }else
                        header("Content-Disposition: inline; filename=\"{$file_name}\"");

                    // set the mime type based on extension

                    $ctype_default = 'text/plain';
                    $content_types = array(
                        'txt'   => 'text/plain',
                        'html'   => 'text/html',
                        'json'  => 'application/json',
                        'xml'   => 'application/xml',
                    );

                    $ctype = isset($content_types[$file_ext]) ? $content_types[$file_ext] : $ctype_default;
                    header("Content-Type: {$ctype}");

                    //check if http_range is sent by brwoser (or download manger)
                    if (isset($_SERVER['HTTP_RANGE'])) {
                        list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
                        if ($size_unit == 'bytes') {
                            list($range, $extra_ranges) = explode(',', $range_orig, 2);
                        } else {
                            $range = '';
                            header('HTTP/1.1 416 Requested Range Not Satisfiable');
                            exit;
                        }

                    } else
                        $range = '';

                    //figure out download piece from range (if set)
                    list($seek_start, $seek_end) = explode('-', $range, 2);

                    //set start and end based of range (if set), else set default
                    //also check for invalid ranges
                    ob_clean();

                    $seek_end = (empty($seek_end)) ? ($file_size - 1) : min(abs(intval($seek_end)), ($file_size - 1));
                    $seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)), 0);

                    //only send partial content header if downloading a piece of the file (IE workaround)
                    if ($seek_start > 0 || $seek_end < ($file_size - 1)) {
                        header('HTTP/1.1 206 Partial Content');
                        header('Content-Range: bytes ' . $seek_start . '-' . $seek_end . '/' . $file_size);
                        header('Content-Length: ' . ($seek_end - $seek_start + 1));
                    } else
                        header("Content-Length: {$file_size}");

                    header('Accept-Ranges: bytes');

                    set_time_limit(0);
                    fseek($file, $seek_start);

                    while(!feof($file)) {
                        print(@fread($file, 1024 * 8));

                        ob_flush();
                        flush();

                        if (connection_status() != 0) {
                            @fclose($file);
                            exit;
                        }
                    }

                    // file save was a success
                    @fclose($file);
                    exit;
                } else {
                    // file couldn't be opened
                    header("HTTP/1.0 500 Internal Server Error");
                    exit;
                }
            } else
                // file does not exist
                header("HTTP/1.0 404 Not Found");
            exit;
            //  return $this->response;
        } catch (\PDOException $e) {
            if ($e->getCode() == 12004) {
                throw new ServiceException('Can not find file', self::ERROR_UNABLE_TO_FIND_FILES);
            } else {
                throw new ServiceException($e->getMessage(). $e->getCode(), $e);            }
        }catch (\Exception $e){
            $this->response->setStatusCode(500, 'Internal Server Error');
        }
    }

    /**
     * Gives a list of files and their clients in return
     * @param
     * @return array
     */
    public function getFileList() {

        try {

            // get user id
            $userId = $this->session->get("userId");

            //used to modify the files array
            $index = 0;

            // looking for user´s files
            $files =$this->modelsManager->createBuilder()
                ->columns(["name" => "Files.name", "extension" => "Files.extension", "version" => "Files.version", "date" => "Files.date"])
                ->from(['TagsFiles'=>'App\Models\TagsFiles'])
                ->leftJoin('App\Models\Files', 'TagsFiles.file_id = Files.id', 'Files')
                ->leftJoin('App\Models\Tags', 'TagsFiles.tag_id = Tags.id', 'Tags')
                ->where('Files.userId = ?1', [1 => $userId] )
                ->getQuery()
                ->execute();

            // change resultset to array
            $files->setHydrateMode(
                Resultset::HYDRATE_ARRAYS
            );

            // looking for user´ tagId´s so he can finds the client
            $tagIds =$this->modelsManager->createBuilder()
                ->columns(["tagId" => "Tags.id"])
                ->from(['TagsFiles'=>'App\Models\TagsFiles'])
                ->leftJoin('App\Models\Files', 'TagsFiles.file_id = Files.id', 'Files')
                ->leftJoin('App\Models\Tags', 'TagsFiles.tag_id = Tags.id', 'Tags')
                ->where('Files.userId = ?1', [1 => $userId] )
                ->getQuery()
                ->execute();
            $tagIds->toArray();

            // use tagId to find the correct client
            foreach ($tagIds as $tagId) {
                $count = 0;

                $tag = ClientsTags::findFirst(
                    [
                        "tag_id = :tag:",
                        "bind" => ["tag" => $tagId['tagId']],
                    ]);

                $clients = $tag->client;

                // modify the files array and add the clientName
                foreach ($files as $file){
                    if ($index == $count) {
                        $result[] = array("no" => $index+1) + array_slice($file, 0, 5, true) +
                            array("clientName" => $clients->name); //+
                        // array_slice($file, 3, count($file) - 1, true);
                    }
                    $count++;
                }
                $index++;
            }

            if (!$result) {
                return [];
            }

            return $result;

        } catch (\PDOException $e) {
            if ($e->getCode() == 12004) {
                throw new ServiceException('Cannot find files', self::ERROR_UNABLE_TO_FIND_FILES);
            } else {
                throw new ServiceException($e->getMessage(). $e->getCode(), $e);            }
        }catch (\Exception $e){
            $this->response->setStatusCode(500, 'Internal Server Error');
        }
    }

    /**
     * Gives a list of files and their clients in return
     * @param $data
     * @return array
     */
    public function getSpecificFileList( array $data) {

        try {

            // get user id
            $userId = $this->session->get("userId");

            //used to modify the files array
            $index = 0;

            // find tag in database
            if (array_keys($data)[0] == 'tag') {

                $tag = Tags::findFirst(
                    [
                        "name = :tag:",
                        "bind" => ["tag" => $data[array_keys($data)[0]]],
                    ]);


                // looking for user´s files
                $files =$this->modelsManager->createBuilder()
                    ->columns(["name" => "Files.name", "extension" => "Files.extension", "version" => "Files.version", "date" => "Files.date"])
                    ->from(['TagsFiles'=>'App\Models\TagsFiles'])
                    ->leftJoin('App\Models\Files', 'TagsFiles.file_id = Files.id', 'Files')
                    ->leftJoin('App\Models\Tags', 'TagsFiles.tag_id = Tags.id', 'Tags')
                    ->where('Files.userId = ?1 AND Tags.id = ?2',
                        [1 => $userId,
                            2 => $tag->id
                        ])
                    ->getQuery()
                    ->execute();

                // change resultset to array
                $files->setHydrateMode(
                    Resultset::HYDRATE_ARRAYS
                );

                $tagIds =$this->modelsManager->createBuilder()
                    ->columns(["tagId" => "Tags.id"])
                    ->from(['TagsFiles'=>'App\Models\TagsFiles'])
                    ->leftJoin('App\Models\Files', 'TagsFiles.file_id = Files.id', 'Files')
                    ->leftJoin('App\Models\Tags', 'TagsFiles.tag_id = Tags.id', 'Tags')
                    ->where('Files.userId = ?1 AND Tags.name = ?2',
                        [1 => $userId,
                            2 => $data[array_keys($data)[0]]
                        ])
                    ->getQuery()
                    ->execute();
                $tagIds->toArray();

            }else{

                // looking for user´s files
                $files =$this->modelsManager->createBuilder()
                    ->columns(["name" => "Files.name", "extension" => "Files.extension", "version" => "Files.version", "date" => "Files.date"])
                    ->from(['TagsFiles'=>'App\Models\TagsFiles'])
                    ->leftJoin('App\Models\Files', 'TagsFiles.file_id = Files.id', 'Files')
                    ->leftJoin('App\Models\Tags', 'TagsFiles.tag_id = Tags.id', 'Tags')
                    ->where('Files.userId = ?1 AND Files.'.array_keys($data)[0].' = ?2',
                        [1 => $userId,
                            2 => $data[array_keys($data)[0]]
                        ])
                    ->getQuery()
                    ->execute();


                // change resultset to array
                $files->setHydrateMode(
                    Resultset::HYDRATE_ARRAYS
                );

                // looking for user´ tagId´s so he can finds the client
                $tagIds =$this->modelsManager->createBuilder()
                    ->columns(["tagId" => "Tags.id"])
                    ->from(['TagsFiles'=>'App\Models\TagsFiles'])
                    ->leftJoin('App\Models\Files', 'TagsFiles.file_id = Files.id', 'Files')
                    ->leftJoin('App\Models\Tags', 'TagsFiles.tag_id = Tags.id', 'Tags')
                    ->where('Files.userId = ?1 AND Files.'.array_keys($data)[0].' = ?2',
                        [1 => $userId,
                            2 => $data[array_keys($data)[0]]
                        ])
                    ->getQuery()
                    ->execute();
                $tagIds->toArray();

            }


            // use tagIds to find the correct client
            foreach ($tagIds as $tagId) {
                $count = 0;

                $tag = ClientsTags::findFirst(
                    [
                        "tag_id = :tag:",
                        "bind" => ["tag" => $tagId['tagId']],
                    ]);

                $clients = $tag->client;

                // modify the files array and add the clientName
                foreach ($files as $file){
                    if ($index == $count) {
                        $result[] = array("no" => $index+1) + array_slice($file, 0, 5, true) +
                            array("clientName" => $clients->name); //+
                        // array_slice($file, 3, count($file) - 1, true);
                    }
                    $count++;
                }
                $index++;
            }

            if (!$result) {
                return [];
            }

            return $result;

        } catch (\PDOException $e) {
            if ($e->getCode() == 12004) {
                throw new ServiceException('Cannot find files', self::ERROR_UNABLE_TO_FIND_FILES);
            } else {
                throw new ServiceException($e->getMessage(). $e->getCode(), $e);            }
        }catch (\Exception $e){
            $this->response->setStatusCode(500, 'Internal Server Error');
        }



    }

    /**
     * deletes the specific file(s)
     *
     * @param array $data
     */
    public function deleteFile(array $data) {

        try {

            $entries = sizeof($data);
            // check size input params
            if ($entries == 1)
            {
                $files =$this->modelsManager->createBuilder()
                    ->columns('TagsFiles.*')
                    ->from(['TagsFiles'=>'App\Models\TagsFiles'])
                    ->leftJoin('App\Models\Files', 'TagsFiles.file_id = Files.id', 'Files')
                    ->leftJoin('App\Models\Tags', 'TagsFiles.tag_id = Tags.id', 'Tags')
                    ->where('Tags.name = ?1', [1 => $data[array_keys($data)[0]]] )
                    ->getQuery()
                    ->execute();

            }elseif ($entries == 4) {

                // get user id
                $userId = $this->session->get("userId");

                // find correct tagId
                $tag = Tags::findFirst(
                    [
                        "name = :tag:",
                        "bind" => ["tag" => $data['tag']],
                    ]);

                $single = explode(".", $data['file_name']);
                $search['name'] = $single[0];
                $search['extension'] = $single[1];

                // looking for user´s files
                $files =$this->modelsManager->createBuilder()
                    ->columns('TagsFiles.*')
                    ->from(['TagsFiles'=>'App\Models\TagsFiles'])
                    ->leftJoin('App\Models\Files', 'TagsFiles.file_id = Files.id', 'Files')
                    ->leftJoin('App\Models\Tags', 'TagsFiles.tag_id = Tags.id', 'Tags')
                    ->where('Files.userId = ?1 AND Tags.id = ?2 AND Files.'.array_keys($search)[0].' = ?3 AND Files.'.array_keys($search)[1].' = ?4 
                    AND Files.version = ?5 AND Files.date = ?6',
                        [1 => $userId,
                            2 => $tag->id,
                            3 => $search[array_keys($search)[0]],
                            4 => $search[array_keys($search)[1]],
                            5 => $data['version'],
                            6 => $data['date'],
                        ])
                    ->getQuery()
                    ->execute();

            }

            $count = $files->count();
            // for each file find Client, and Tags
            foreach ($files as $file){

                if ($count == 1 && $file->tag->counttagsFiles() == 1){

                    $client = Clients::findFirst(
                        [
                            'conditions'  => 'name = :name:',
                            'bind'        => [
                                'name'    => $this->session->get("client_id")
                            ]
                        ]
                    );

                    $client_tags = ClientsTags::findFirst(
                        [
                            'conditions'  => 'client_id = :client_id: AND tag_id = :tag_id:',
                            'bind'        => [
                                'client_id'    => $client->id,
                                'tag_id'    => $file->tag->id

                            ]
                        ]
                    );
                    // delete them
                    $client_tags->delete();
                    $file->file->delete();
                    $file->tag->delete();


                }
                $count --;

                $file->file->delete();

                $userId = $this->session->get("userId");
                $dir = FILES_PATH.$userId.'/';
                // delete the file from space
                unlink($dir.$file->file->id.'_'.$file->file->name.'.'.$file->file->extension);
                if ($this->is_dir_empty($dir)) {
                    rmdir($dir);
                }
            }

        } catch (\PDOException $e) {
            if ($e->getCode() == 12004) {
                throw new ServiceException('Cannot find files', self::ERROR_UNABLE_TO_FIND_FILES);
            } else {
                throw new ServiceException($e->getMessage(). $e->getCode(), $e);            }
        }catch (\Exception $e){
            $this->response->setStatusCode(500, 'Internal Server Error');
        }

    }


    /**
     * helper class to check if the user dir is empty
     *
     * @param $dir
     * @return bool
     */
    private function is_dir_empty($dir) {

        try {
            $handle = opendir($dir);
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    return FALSE;
                }
            }
            return TRUE;

        } catch (\Exception $e) {
            $this->response->setStatusCode(500, 'Internal Server Error');
        }
    }

    /**
     * Insert file and all other values into Files DB and insert the realtionship into TagsFiles DB
     *
     * @param $data
     * @param $uploads
     * @return mixed
     */
    private function dataUpload($data, $uploads){
        try {

            $files = new Files();
            $tags = new Tags();
            $tags_files = new TagsFiles();
            $file_name = explode('.', $uploads->getName());
            $userId = $this->session->get("userId");

            $file_id = str_replace('-', '', $data['fileId']);
            $tagsFilesId = str_replace('-', '', $data['tagsFilesId']);
            // set databse entries for files
            $result = $files->setId($file_id)
                ->setName($file_name[0])
                ->setDate($data['current_Date']->format('Y-m-d'))
                ->setExtension($uploads->getExtension())
                ->setVersion($data['version'])
                ->setUserId($userId)
                ->create();

            // set database entries for tag
            $tag = $tags->setId($data['tag_id'])
                ->setName($data['tag'])
                ->create();

            // set database entries for tags_files
            $tags_files->setId($tagsFilesId)
                ->setTagId($data['tag_id'])
                ->setFileId($file_id)
                ->create();

            $dir = FILES_PATH.$userId.'/';
            // create user folder
            if (!is_dir($dir)) {
                if (false === @mkdir($dir, 0777, true)) {
                    throw new \RuntimeException(sprintf('Unable to create the %s directory', $dir));
                }
            }
            // create file in space
            $uploads->moveTo($dir.$file_id.'_'.$uploads->getName());

            return $tags_files;

        } catch (\PDOException $e) {
            if ($e->getCode() == 1086) {
                throw new ServiceException('File already exists', self::ERROR_ALREADY_EXISTS);
            }// else {
             //   throw new ServiceException($e->getMessage(). $e->getCode(), $e);            }
        }

    }

    /**
     * Checks if the Tag is existing, unless insert into Tag DB
     *
     * @param $tag
     * @return int
     */
    private function checkTagId($tag){

        try {

            $random = new Random();

            $existing_tags = Tags::findFirst(
                [
                    'conditions'  => 'name = :name:',
                    'bind'        => [
                        'name'    => $tag
                    ]
                ]
            );

            if (!$existing_tags){
                $clients_tags = new ClientsTags();
                $randomClintTagUuid = new Random();
                $tagsClientsUuid = $randomClintTagUuid->Uuid();
                $uuid = $random->Uuid();

                $tag_id = str_replace('-', '', $uuid);

                //findFirst client
                $existing_client = Clients::findFirst(
                    [
                        'conditions'  => 'name = :name:',
                        'bind'        => [
                            'name'    => $this->session->get("client_id")
                        ]
                    ]
                );

                //set clients and tags relationship
                $clients_tags->setId($tagsClientsUuid)
                    ->setClientId($existing_client->id)
                    ->setTagId($tag_id)
                    ->create();

            } else {
                $tag_id = $existing_tags->getId();
            }
            return $tag_id;

        } catch (\PDOException $e) {
            throw new ServiceException($e->getMessage(). $e->getCode(), $e);
        }
    }
}