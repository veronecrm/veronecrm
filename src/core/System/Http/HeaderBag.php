<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Http;

use System\ParameterBag;

class HeaderBag extends ParameterBag
{
    public function __construct(array $headers)
    {
        foreach($headers as $key => $values)
        {
            $this->set($key, $values);
        }
    }

    public function get($key, $default = null, $first = true)
    {
        $key = strtr(strtolower($key), '_', '-');

        if(! isset($this->parameters[$key]))
        {
            if($default === null)
            {
                return $first ? null : array();
            }

            return $first ? $default : array($default);
        }

        if($first)
        {
            return isset($this->parameters[$key][0]) ? $this->parameters[$key][0] : $default;
        }

        return $this->parameters[$key];
    }

    public function set($key, $values, $replace = true)
    {
        $key    = strtr(strtolower($key), '_', '-');
        $values = array_values((array) $values);

        if($replace === true || ! isset($this->parameters[$key]))
        {
            $this->parameters[$key] = $values;
        }
        else
        {
            $this->parameters[$key] = array_merge($this->parameters[$key], $values);
        }

        return $this;
    }

    public function has($key)
    {
        return isset($this->parameters[strtr(strtolower($key), '_', '-')]);
    }

    public function remove($key)
    {
        unset($this->parameters[strtr(strtolower($key), '_', '-')]);

        return $this;
    }
}
