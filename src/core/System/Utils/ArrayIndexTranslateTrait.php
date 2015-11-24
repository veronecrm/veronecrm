<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Utils;

trait ArrayIndexTranslateTrait
{
    /**
     * Create array index, from given string.
     * @param  string $name
     * @return string
     */
    public function createIndex($name)
    {
        return "['".implode("']['", explode('.', $name))."']";
    }

    /**
     * Returns value from given index. If not exists - return null.
     * @param  string $name  Index.
     * @param  array  $array Source array.
     * @return mixed
     */
    public function getFromArray($name, array $array)
    {
        $index    = $this->createIndex($name);
        $function = create_function('$array', 'return (isset($array'.$index.') ? $array'.$index.' : null);');
        return $function($array);
    }

    /**
     * Sets new or update existing value on given index in given array.
     * @param string  $name   Index.
     * @param mixed   $val    Value, to set or update.
     * @param array
     * @return array
     */
    public function setInArray($name, $val, array $array)
    {
        $function = create_function('$array, $val', '$array'.$this->createIndex($name).' = $val; return $array;');
        return $function($array, $val);
    }

    /**
     * Checks if indeks exists in array.
     * @param string  $name   Index.
     * @param array
     * @return boolean
     */
    public function existsInArray($name, array $array)
    {
        $function = create_function('$array', 'return isset($array'.$this->createIndex($name).');');
        return $function($array);
    }

    /**
     * Removes index from array.
     * @param string  $name   Index.
     * @param array
     * @return boolean
     */
    public function removeFromArray($name, array $array)
    {
        $function = create_function('$array, $val', 'unset($array'.$this->createIndex($name).'); return $array;');
        return $function($array, $val);
    }
}
