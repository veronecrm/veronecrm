<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace Helper;

use CRM\Base;

class Currency extends Base
{
    protected $all;
    protected $available;

    /**
     * Returns all available currencies. These currencies can be added to used currencies list.
     * @return array
     */
    public function available()
    {
        if($this->available === null)
        {
            $this->available = $this->db()->builder()->select('*')->from('#__vars_currency')->orderBy('name', 'ASC')->all();
        }

        return $this->available;
    }

    /**
     * Returns all Created currencies, not all available Currencies!
     * @return array
     */
    public function all()
    {
        if($this->all === null)
        {
            $this->all = $this->db()->builder()->select('*')->from('#__currency')->orderBy('name', 'ASC')->all();
        }

        return $this->all;
    }

    /**
     * Checks if Currency ISO Code exists in added currencies list. Not in available list!
     * @param  string $code Currency ISO Code.
     * @return boolean
     */
    public function exists($code)
    {
        $result = $this->db()->builder()->select('*')->from('#__currency')->where('code', $code)->one();

        if($result)
            return true;
        else
            return false;
    }

    /**
     * Returns details about Currency by given ISO Code. Search in Available currencies, not in added!
     * @param  string $code Currency ISO Code.
     * @return object
     */
    public function get($code)
    {
        return $this->db()->builder()->select('*')->from('#__vars_currency')->where('code', $code)->one();
    }

    /**
     * Returns details about current defined default Currency. Search in added currencies!
     * @return object
     */
    public function getDefault()
    {
        return $this->db()->builder()->select('*')->from('#__currency')->where('id', $this->openSettings('app')->get('currency'))->one();
    }

    public function append($qty, $id = null)
    {
        if($id === null)
        {
            $id = $this->openSettings('app')->get('currency');
        }

        foreach($this->all() as $currency)
        {
            if($currency->id === $id)
            {
                return "$qty {$currency->symbol}";
            }
        }

        return "$qty ---";
    }

    public function name($id = null)
    {
        if($id === null)
        {
            $id = $this->openSettings('app')->get('currency');
        }

        foreach($this->all() as $currency)
        {
            if($currency->id === $id)
            {
                return $currency->symbol;
            }
        }

        return '---';
    }
}
