<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\User\Group;

class Entity extends \CRM\ORM\Entity
{
    protected $id;
    protected $parent;
    protected $name;
    protected $announcement;

    /**
     * Gets the id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the $id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the parent.
     *
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the $parent.
     *
     * @param mixed $parent the parent
     *
     * @return self
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Gets the name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the $name.
     *
     * @param mixed $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the announcement.
     *
     * @return mixed
     */
    public function getAnnouncement()
    {
        return $this->announcement;
    }

    /**
     * Sets the $announcement.
     *
     * @param mixed $announcement the announcement
     *
     * @return self
     */
    public function setAnnouncement($announcement)
    {
        $this->announcement = $announcement;

        return $this;
    }
}
