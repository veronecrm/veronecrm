<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Http\Session;

use System\ParameterBag;

class FlashBag
{
    protected $flashes = ['new' => [], 'current' => []];

    public function initialize(array &$flashes)
    {
        $this->flashes = &$flashes;

        $this->flashes['current'] = isset($this->flashes['new']) ? $this->flashes['new'] : [];
        $this->flashes['new']     = [];
    }

    public function all()
    {
        $return = $this->flashes['current'];

        $this->flashes = ['new' => [], 'current' => []];

        return $return;
    }

    public function set($type, $messages)
    {
        $this->flashes['new'][$type] = (array) $messages;

        return $this;
    }

    public function setAll(array $messages)
    {
        $this->flashes['new'] = $messages;

        return $this;
    }

    public function get($type, array $default = array())
    {
        $return = $default;

        if(! $this->has($type))
        {
            return $return;
        }

        if(isset($this->flashes['current'][$type]))
        {
            $return = $this->flashes['current'][$type];
            unset($this->flashes['current'][$type]);
        }

        return $return;
    }

    public function add($key, $value)
    {
        $this->flashes['new'][$key][] = $value;

        return $this;
    }

    public function has($type)
    {
        return isset($this->flashes['current'][$type]) && $this->flashes['current'][$type];
    }

    public function count($type)
    {
        return count($this->flashes['current'][$type]);
    }

    public function remove($key)
    {
        unset($this->flashes['current'][$type]);

        return $this;
    }

    public function keys()
    {
        return array_keys($this->flashes['current']);
    }
}
