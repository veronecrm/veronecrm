<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Settings;

class Settings
{
    /**
     * Store providers.
     * 
     * @var array
     */
    private $providers = [];

    /**
     * Store object of Cache.
     * 
     * @var Cache
     */
    private $cache;

    /**
     * Create default Cache object.
     */
    public function __construct()
    {
        $this->setCache(new Cache);
    }

    /**
     * Set Cache object.
     *
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get Cache object.
     *
     * @return Cache
     */
    public function cache()
    {
        return $this->cache;
    }

    /**
     * Register provider object.
     *
     * @param  string   $name     Name of setting or provider.
     * @param  Provider $provider Provider object.
     * @return self
     */
    public function register($name, Provider $provider)
    {
        $this->providers[$name] = $provider;

        return $this;
    }

    /**
     * Open provider of given name, and pass parameter to it.
     * 
     * @param  string  $name  Name of setting or provider.
     * @param  integer $param Additionally parameter.
     * @return Provider|boolean
     */
    public function open($name, $param = 0)
    {
        if(isset($this->providers[$name]))
        {
            return $this->providers[$name]
                ->factory()
                ->setCache($this->cache)
                ->setParam($param);
        }

        return false;
    }
}
