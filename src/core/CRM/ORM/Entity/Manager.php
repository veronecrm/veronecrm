<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\ORM\Entity;

use CRM\ORM\ORM;

class Manager
{
    /**
     * @var ORM
     */
    private $orm;

    /**
     * @param ORM $orm
     */
    public function __construct(ORM $orm)
    {
        $this->orm = $orm;
    }

    /**
     * Returns info, if Entity class exists.
     * @param  string $name   Entity name.
     * @param  string $module Name of module (if is given). Default is taken from ORM object.
     * @return boolean
     */
    public function exists($name = null, $module = null)
    {
        $className = $this->generateClassName($name, $module);

        return class_exists($className, true);
    }

    /**
     * Returns Entity object of given name.
     * @param  string $name      Entity name.
     * @param  string $module Name of module (if is given). Default is taken from ORM object.
     * @return System\ORM\Entity
     */
    public function get($name, $module = null)
    {
        $className = $this->generateClassName($name, $module);

        return new $className();
    }

    /**
     * Generate name of Entity class.
     * @param  string $name   Name of Entity.
     * @param  string $module Name of module (if is given). Default is taken from ORM object.
     * @return string
     */
    public function generateClassName($name = null, $module = null)
    {
        if($module == '')
        {
            $module = $this->orm->getModule();
        }
    
        if($name == '')
        {
            $name = $module;
        }
        
        return "App\\Module\\{$module}\\ORM\\{$name}";
    }
}
