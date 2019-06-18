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

class Clients extends Model
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
     * @Column(type="varchar", length=50, nullable=false)
     */
    protected $name;

    /**
     * @var string
     * @Column(type="varchar", length=200, nullable=false)
     */
    protected $preview_pic;

    /**
     * @var string
     * @Column(type="varchar", length=250, nullable=false)
     */
    protected $short_description;

    /**
     * @var string
     * @Column(type="varchar", length=250, nullable=false)
     */
    protected $redirect;

    public function initialize()
    {
        $this->hasManytoMany(
            'id',
            'App\Models\ClientsTags',
            'client_id', 'tag_id',
            'App\Models\Tags',
            'id',
            array(
                'alias'=>'tags',
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
    public function setClientId($id)
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
     * Returns the value of the field preview_pic
     *
     * @return string
     */
    public function getPreviewPic()
    {
        return $this->preview_pic;
    }

    /**
     * Method to set the value of field preview_pic
     *
     * @param string $preview_pic
     * @return $this
     */
    public function setPreviewPic($preview_pic)
    {
        $this->preview_pic = $preview_pic;

        return $this;
    }

    /**
     * Returns the value of the field short_description
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->short_description;
    }

    /**
     * Method to set the value of field short_description
     *
     * @param string $short_description
     * @return $this
     */
    public function setShortDescription($short_description)
    {
        $this->short_description = $short_description;

        return $this;
    }

    /**
     * Returns the value of the field redirect
     *
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * Method to set the value of field redirect
     *
     * @param string $redirect
     * @return $this
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     *
     * @return Users[]|Users
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }




}