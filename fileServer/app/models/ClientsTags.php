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

class ClientsTags extends Model
{
    /**
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $client_id;

    /**
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $tag_id;

    public function initialize()
    {
        $this->belongsTo(
            'client_id',
            'App\Models\Clients',
            'id',
            array(
                'alias'=>'client',
            )
        );

        $this->belongsTo(
            'tag_id',
            'App\Models\Tags',
            'id',
            array(
                'alias'=>'tag',
            )
        );
    }

    /**
     * Retruns  the value of field id
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
     * Returns the value of the field client_id
     *
     * @return integer
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * Method to set the value of field client_id
     *
     * @param integer $client_id
     * @return $this
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;

        return $this;
    }

    /**
     * Returns the value of the field tag_id
     *
     * @return integer
     */
    public function getTagId()
    {
        return $this->tag_id;
    }

    /**
     * Method to set the value of field tag_id
     *
     * @param integer $tag_id
     * @return $this
     */
    public function setTagId($tag_id)
    {
        $this->tag_id = $tag_id;

        return $this;
    }

}