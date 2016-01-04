<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Routing;

class Route
{
    private $module     = 'Home';
    private $controller = 'Home';
    private $action     = 'index';

    public function __construct($module = 'Home', $controller = 'Home', $action = 'index')
    {
        $this->module     = $module;
        $this->controller = $controller;
        $this->action     = $action;
    }

    public static function generateStringFromParams(array $params = [])
    {
        return http_build_query(array_merge([
            'mod' => 'Home',
            'cnt' => 'Home',
            'act' => 'index'
        ], $params));
    }

    /**
     * Gets the module.
     *
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Sets the $module.
     *
     * @param mixed $module the module
     *
     * @return self
     */
    public function setModule($module)
    {
        if(! $module)
        {
            return $this;
        }

        $this->module = $module;

        return $this;
    }

    /**
     * Gets the controller.
     *
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Sets the $controller.
     *
     * @param mixed $controller the controller
     *
     * @return self
     */
    public function setController($controller)
    {
        if(! $controller)
        {
            return $this;
        }

        $this->controller = $controller;

        return $this;
    }

    /**
     * Gets the action.
     *
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Sets the $action.
     *
     * @param mixed $action the action
     *
     * @return self
     */
    public function setAction($action)
    {
        if(! $action)
        {
            return $this;
        }

        $this->action = $action;

        return $this;
    }
}
