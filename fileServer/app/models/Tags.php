<?php
/**
 * Project: Hawai FileServer
 * Author: Andreas Jäckel
 * Last Change:
 *  by: Andreas Jäckel
 *  date: 18.04.18
 * Copyright (c): Hawai Project, 2018
 */

namespace App\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Relation;

class Tags extends Model
{
    /**
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    public $name;

    public function initialize()
    {
        $this->hasManytoMany(
            'id',
            'App\Models\ClientsTags',
            'tag_id', 'client_id',
            'App\Models\Clients',
            'id',
            array(
                'alias'=>'clients',
            )
        );

        $this->hasManytoMany(
            'id',
            'App\Models\TagsFiles',
            'tag_id', 'file_id',
            'App\Models\Files',
            'id',
            array(
                'alias' => 'tagsFiles',
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



}