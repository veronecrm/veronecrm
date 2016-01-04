<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Translation;

use System\Config\Config;

class Container
{
    private $content;
    private $locale;

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function attachFromConfig(Config $config, $locale = null)
    {
        $locale = $locale ? $locale : $this->locale;

        $this->content[$locale] = isset($this->content[$locale]) ? array_merge($config->all(), $this->content[$locale]) : $config->all();

        return $this;
    }

    public function has($key)
    {
        return isset($this->content[$this->locale][$key]);
    }

    public function get($key)
    {
        if(isset($this->content[$this->locale][$key]))
        {
            return $this->content[$this->locale][$key];
        }
        else
        {
            return $key;
        }
    }
}
