<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\ORM\Repository;

use CRM\ORM\ORM;
use System\DependencyInjection\Container;

class Manager
{
    /**
     * @var ORM
     */
    private $orm;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param ORM       $orm
     * @param Container $container
     */
    public function __construct(ORM $orm, Container $container)
    {
        $this->orm       = $orm;
        $this->container = $container;
    }

    /**
     * Returns info, if Repository class exists.
     * @param  string $name   Repository name.
     * @param  string $module Name of module (if is given). Default is taken from ORM object.
     * @return boolean
     */
    public function exists($name = null, $module = null)
    {
        $className = $this->generateClassName($name, $module);

        return class_exists($className, true);
    }

    /**
     * Returns Repository object of given name.
     * @param  string $name   Repository name.
     * @param  string $module Name of module (if is given). Default is taken from ORM object.
     * @return System\ORM\Repository
     */
    public function get($name = null, $module = null)
    {
        $className = $this->generateClassName($name, $module);

        $repoObject = new $className($name, $module, $this->orm);
        $repoObject->setContainer($this->container);
        $repoObject->setSourceEntity($this->orm->entity()->get($name, $module));

        return $repoObject;
    }

    /**
     * Generate name of Entity class.
     * @param  string $name   Repository name.
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
        
        return "App\\Module\\{$module}\\ORM\\{$name}Repository";
    }
}
