<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Settings;

/**
 * Default Settings Cache class.
 */
class Cache
{
    /**
     * Stores values.
     * 
     * @var array
     */
    protected $values = [];

    /**
     * Checks if the value of given parameters exists in Cache.
     * 
     * @param  string  $key    Provider name.
     * @param  string  $name   Setting name.
     * @param  integer  $type  Type.
     * @param  integer  $param Parameter.
     * @return boolean
     */
    public function has($key, $name, $type, $param)
    {
        return isset($this->values[$key][$name][$type][$param]);
    }

    /**
     * Gets value of given params from Cache (if exists).
     * 
     * @param  string  $key    Provider name.
     * @param  string  $name   Setting name.
     * @param  integer  $type  Type.
     * @param  integer  $param Parameter.
     * @return boolean
     */
    public function get($key, $name, $type, $param)
    {
        if(isset($this->values[$key][$name][$type][$param]))
        {
            return $this->values[$key][$name][$type][$param];
        }

        return false;
    }


    /**
     * Sets value of given params to Cache.
     * 
     * @param  string  $key    Provider name.
     * @param  string  $name   Setting name.
     * @param  integer  $type  Type.
     * @param  integer  $param Parameter.
     * @param  mixed    $value Value to save in Cache.
     * @return self
     */
    public function set($key, $name, $type, $param, $value)
    {
        $this->values[$key][$name][$type][$param] = $value;

        return $this;
    }

    /**
     * Save values in Cache for next usage.
     * 
     * @return boolean
     */
    public function save()
    {

    }

    /**
     * Get values from Cache to use,
     * 
     * @return boolean
     */
    public function retrieve()
    {

    }
}
