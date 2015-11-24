<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Utils;

/**
 * Class converts object properties and values (even private) into JSON string.
 */
class ObjectToJSONConverter
{
    protected $src;

    /**
     * Construct.
     * @param object $src object to convert.
     */
    public function __construct($src)
    {
        $this->src = $src;
    }

    /**
     * Convert object to JSON, and return string contains properties names and its values.
     * @return string
     */
    public function convert()
    {
        return json_encode((new ObjectToArrayConverter($this->src))->convert());
    }
}
