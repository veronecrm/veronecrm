<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System;

class ParameterBag
{
    protected $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function initialize(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function all()
    {
        return $this->parameters;
    }

    public function has($key)
    {
        return isset($this->parameters[$key]);
    }

    public function get($key, $default = null)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function remove($key)
    {
        unset($this->parameters[$key]);

        return $this;
    }

    public function count()
    {
        return count($this->parameters);
    }

    public function keys()
    {
        return array_keys($this->parameters);
    }

    public function replace(array $parameters = [])
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function add(array $parameters = [])
    {
        $this->parameters = array_replace($this->parameters, $parameters);

        return $this;
    }
}
