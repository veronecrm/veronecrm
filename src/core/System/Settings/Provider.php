<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Settings;

/**
 * Abstract provider for Settings Data manage.
 */
abstract class Provider
{
    /**
     * Stores type of key. Value stored also in DB rows.
     * 
     * @var integer
     */
    protected $type = 0;

    /**
     * Stores additionally parameter for rows. Value stored also in DB rows.
     * 
     * @var integer
     */
    protected $param = 0;

    /**
     * Stores object of Cache.
     * 
     * @var Cache
     */
    protected $cache;

    /**
     * Gets type.
     * 
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets Parameter for rows.
     * 
     * @param  integer $param
     * @return self
     */
    public function setParam($param)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Gets current rows parameter.
     * 
     * @return integer
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Method creates an instance of self or another compatible
     * class. In controller we can have opened many keys with
     * different parameters.
     * 
     * @return self
     */
    public function factory()
    {
        return clone $this;
    }

    /**
     * Sets Cache object.
     *
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return false if value does not exists. If exists, returns it's ID.
     *
     * @param  string  $name Name of value.
     * @return boolean|integer
     */
    abstract public function has($name);

    /**
     * Return value of given name.
     *
     * @param  string  $name Name of value.
     * @return mixed
     */
    abstract public function get($name);

    /**
     * Return true, if key and value exists and if value was saved.
     * Otherwise return false.
     *
     * @param  string  $name Name of value.
     * @param  mixed   $value Value to save.
     * @return boolean
     */
    abstract public function set($name, $value);

    /**
     * Saves new key with default value.
     * 
     * @param  string $name    Key name.
     * @param  mixed  $default Default value.
     * @return boolean
     */
    abstract public function registerKey($name, $default);

    /**
     * Removes key.
     * 
     * @param  string $name Key name.
     * @return boolean
     */
    abstract public function unregisterKey($name);
}
