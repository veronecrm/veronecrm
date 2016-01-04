<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Templating;

use Atline\Atline\DefinitionResolverInterface;
use System\Routing\Route;

class DefinitionResolver implements DefinitionResolverInterface
{
    private $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function resolve($definition)
    {
        if(is_string($definition))
        {
            if($definition == '')
            {
                $definition = $this->route->getAction();

                return BASEPATH.'/app/App/Module/'.$this->route->getModule().'/View/'.$this->route->getController()."/{$definition}.tpl";
            }

            $exploded = explode('.', $definition);

            // View from Current module
            if(count($exploded) === 1)
            {
                return BASEPATH.'/app/App/Module/'.$this->route->getModule().'/View/'.$this->route->getController()."/{$definition}.tpl";
            }

            // Main template path
            if(isset($exploded[0]) && $exploded[0] === 'master' && isset($exploded[1]))
            {
                return BASEPATH."/app/Template/{$exploded[1]}.tpl";
            }

            // Global view
            if(isset($exploded[0]) && $exploded[0] === 'global' && isset($exploded[1]))
            {
                return BASEPATH."/app/Template/Global/{$exploded[1]}.tpl";
            }

            // View from selected module
            $module     = isset($exploded[2]) ? $exploded[2] : $this->route->getModule();
            $controller = isset($exploded[1]) ? $exploded[1] : $this->route->getController();
            $action     = isset($exploded[0]) ? $exploded[0] : $this->route->getAction();
            
            /**
             * If one of the segments are empty, place between slashes is empty too, so
             * we must remove double shades. It's nedded sometimes for render view placed
             * not in controller filder, but directly in View folder.
             */
            return str_replace('//', '/', BASEPATH."/app/App/Module/{$module}/View/{$controller}/{$action}.tpl");
        }

        return '';
    }
}
