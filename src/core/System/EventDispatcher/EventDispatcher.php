<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\EventDispatcher;

class EventDispatcher
{
    private $listeners = [];

    public function addListener($name, $listener)
    {
        if(! isset($this->listeners[$name]))
        {
            $this->listeners[$name] = [];
        }

        if(is_object($listener))
        {
            $this->listeners[$name][] = $listener;
        }
        elseif(is_string($listener))
        {
            if(class_exists($listener, true))
            {
                $this->listeners[$name][] = new $listener;
            }
        }

        return $this;
    }

    public function dispatch($name, array $params = [])
    {
        $result = [];

        if(! isset($this->listeners[$name]))
        {
            return $result;
        }

        foreach($this->listeners[$name] as $listener)
        {
            if(is_callable([ $listener, $name ]))
            {
                $result[] = call_user_func_array( [ $listener, $name ], $params);
            }
        }

        return $result;
    }
}
