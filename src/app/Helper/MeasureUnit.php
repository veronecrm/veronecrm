<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace Helper;

use CRM\Base;

class MeasureUnit extends Base
{
    protected $units;

    public function all()
    {
        if($this->units === null)
        {
            $this->units = $this->db()->builder()->select('*')->from('#__measure_unit')->all();
        }

        return $this->units;
    }

    public function append($qty, $unitId)
    {
        foreach($this->all() as $unit)
        {
            if($unit->id === $unitId)
            {
                return "$qty {$unit->unit}";
            }
        }

        return "$qty ---";
    }

    public function name($unitId)
    {
        foreach($this->all() as $unit)
        {
            if($unit->id === $unitId)
            {
                return $unit->unit;
            }
        }

        return '---';
    }
}
