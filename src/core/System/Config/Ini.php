<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Config;

class Ini extends Config
{
    public function __construct($filepath)
    {
        $this->params = parse_ini_file($filepath, true);
    }
}
