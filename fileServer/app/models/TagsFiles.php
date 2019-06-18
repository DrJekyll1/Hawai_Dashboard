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

class TagsFiles extends Model
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
    public $tag_id;

    /**
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $file_id;

    public function initialize()
    {
        $this->belongsTo(
            'file_id',
            'App\Models\Files',
            'id',
            array(
                'alias'=> 'file',
                'reusable' => true,
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
     * Returns the value of the field file_id
     *
     * @return integer
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * Method to set the value of field file_id
     *
     * @param integer $file_id
     * @return $this
     */
    public function setFileId($file_id)
    {
        $this->file_id = $file_id;

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