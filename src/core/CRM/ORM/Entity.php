<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\ORM;

use System\Http\Request;

class Entity
{
    /**
     * Tells if Entity is New. If is new, it will be SAVED, otherwise will be UPDATED.
     * @var boolean
     */
    public $isNew = true;

    /**
     * Returns Entity class name (without namespace).
     * @return string Class name.
     */
    public function getEntityName()
    {
        $exploded = explode('\\', get_class($this));
        return end($exploded);
    }

    /**
     * Returns Module class name, which this object is contained.
     * @return string Module name.
     */
    public function getModuleName()
    {
        return explode('\\', get_class($this))[2];
    }

    /**
     * Return array of fields of Entity.
     * @param  object $object
     * @return array
     */
    public function getColumns($object = null)
    {
        return array_keys(array_diff_key(get_class_vars(get_class($object ? $object : $this)), get_class_vars(get_parent_class($object ? $object : $this))));
    }

    /**
     * Fills properties of Entity by values from given Request.
     * @param  Request $request
     * @return self
     */
    public function fillFromRequest(Request $request)
    {
        foreach($this->getColumns() as $column)
        {
            $method = $this->createAccessMethod('set', $column);

            // Only if Request exists
            if($request->request->has($column))
            {
                if(method_exists($this, $method))
                {
                    $this->{$method}($request->request->get($column));
                }
                else
                {
                    $this->{$column} = $request->request->get($column);
                }
            }
        }

        return $this;
    }

    /**
     * Exports all data from entity into array. Array key is a field name.
     * @return array Array of data.
     */
    public function exportToArray()
    {
        $result = [];

        foreach($this->getColumns() as $column)
        {
            $result[$column] = $this->{$this->createAccessMethod('get', $column)}();
        }

        return $result;
    }

    /**
     * Returns Repository of this Entity class name. With or without
     * namespace - if $full is given.
     * @param  boolean $full  Result class name with namespace?
     * @return string         Class name of Repository of this Entity.
     */
    public function getRepositoryClassName($full = true)
    {
        if($full)
        {
            list( , , $module, , $name) = explode('\\', get_class($this));
            return "App\\Module\\{$module}\\ORM\\{$name}Repository";
        }
        else
        {
            return $this->getEntityName().'Repository';
        }
    }

    /**
     * Create property value method name by given type (getter or setter).
     * @param  string $type set OR get
     * @param  string $name Name of property
     * @return string
     */
    public function createAccessMethod($type, $name)
    {
        return $type == 'get' ? 'get'.ucfirst($name) : 'set'.ucfirst($name);
    }
}
