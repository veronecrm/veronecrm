<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace Helper;

use CRM\Base;

class Language extends Base
{
    public function all()
    {
        return $this->db()->builder()->select('*')->from('#__vars_language')->orderBy('name', 'ASC')->all();
    }
}
