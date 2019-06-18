<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 02.05.18
 * Copyright (c): Hawai Project, 2018
 */

namespace App\Models;

use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model;

class Files extends Model
{
    /**
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     * @var string
     * @Column(type="varchar", length=200, nullable=false)
     */
    protected $name;

    /**
     * @var string
     * @Column(type="varchar", length=5, nullable=false)
     */
    protected $extension;

    /**
     * @var string
     * @Primary
     * @Identity
     * @Column(type="varchar", length=5, nullable=false)
     */
    protected $version;

    /**
     * @var Timestamp
     * @Column(type="TIMESTAMP", nullable=false)
     */
    protected $date;

    protected $userId;

    public function initialize()
    {
        $this->hasManytoMany(
            'id',
            'App\Models\TagsFiles',
            'file_id', 'tag_id',
            'App\Models\Tags',
            'id',
            array(
                'alias'=>'tags',
            )
        );
        $this->hasMany(
            'id',
            'App\Models\TagsFiles',
            'file_id',
             array(
                 'alias'=>'tagsFiles',
           //      'foreignKey' => array(
           //          'action' => Relation::ACTION_CASCADE,
           //      )
             )
        );
    }

    /**
     * Returns the value of the field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns the value of the field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the value of the field name
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Method to set the value of field extension
     *
     * @param string $extension
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Returns the value of the field veriosn
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Method to set the value of field version
     *
     * @param integer $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Returns the value of the field date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Method to set the value of field date
     *
     * @param string $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Returns the value of the field userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Method to set the value of field userId
     *
     * @param integer $id
     * @return $this
     */
    public function setUserId($id)
    {
        $this->userId = $id;

        return $this;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     *
     * @return tags[]/tag
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return tag
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }


}