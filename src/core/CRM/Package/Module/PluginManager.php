<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Module;

use System\DependencyInjection\Container;
use System\Settings\Settings;

class PluginManager
{
    private $container;
    private $moduleManager;
    private $settings;

    public function __construct(Container $container, ModuleManager $moduleManager, Settings $settings)
    {
        $this->container      = $container;
        $this->moduleManager  = $moduleManager;
        $this->settings       = $settings;
    }

    /**
     * Search for plugins in active modules, and call action method
     * passes optional parameters.
     *
     * @param string $category Category of plugins.
     * @param string $action   Action name to call.
     * @param array  $params   Array of params to pass to action.
     * @return array           Array of results from each plugin.
     */
    public function callPlugins($category, $action, array $params = [])
    {
        $result = [];

        foreach($this->moduleManager->all() as $module)
        {
            $className  = "App\Module\\{$module->getName()}\Plugin\\{$category}";

            if(class_exists($className, true))
            {
                $plugin     = new $className;
                $plugin->setContainer($this->container);

                if(method_exists($plugin, $action))
                {
                    $result[] = call_user_func_array(array($plugin, $action), $params);
                }
            }
        }

        return $result;
    }

    /**
     * Search for plugin in given module, and call action method
     * passes optional parameters.
     *
     * @param string $module   Module name where plugin places.
     * @param string $category Category of plugin.
     * @param string $action   Action name to call.
     * @param array  $params   Array of params to pass to action.
     * @return array           Results from plugin.
     */
    public function callPlugin($module, $category, $action, array $params = [])
    {
        $className  = "App\Module\\{$module}\Plugin\\{$category}";
        $result     = null;

        if(class_exists($className, true))
        {
            $plugin     = new $className;
            $plugin->setContainer($this->container);

            if(method_exists($plugin, $action))
            {
                $result = call_user_func_array(array($plugin, $action), $params);
            }
        }

        return $result;
    }

    /**
     * Search for plugins in active modules, and check if plugin
     * have method that respond on given event name.
     *
     * @param string $category Category of plugins.
     * @param string $action   Action name to call.
     * @return array           Array of responded modules.
     */
    public function getPluginsRespondedOn($category, $action)
    {
        $result = [];

        foreach($this->moduleManager->all() as $module)
        {
            $className  = "App\Module\\{$module->getName()}\Plugin\\{$category}";

            if(class_exists($className, true))
            {
                $plugin = new $className;
                $plugin->setContainer($this->container);

                if(method_exists($plugin, $action))
                {
                    $result[] = $module;
                }
            }
        }

        return $result;
    }
}
