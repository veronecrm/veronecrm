<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Config;

class Php extends Config
{
    public function __construct($filepath)
    {
        $this->params = include($filepath);
    }
}
