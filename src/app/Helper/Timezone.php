<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace Helper;

use CRM\Base;

class Timezone extends Base
{
    public function all()
    {
        return $this->db()->builder()->select('*')->from('#__vars_timezone')->orderBy('name', 'ASC')->all();
    }
}
