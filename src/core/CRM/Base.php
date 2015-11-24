<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM;

use System\DependencyInjection\Container;
use System\Routing\Route;
use CRM\History\User\EntityLog;

class Base
{
    protected $container;

    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    public function get($name)
    {
        return $this->container->get($name);
    }

    public function request()
    {
        return $this->container->get('request');
    }

    public function user()
    {
        return $this->container->get('user');
    }

    public function flash($type, $value)
    {
        $this->request()->getSession()->getFlashBag()->add($type, $value);

        return $this;
    }

    public function flashBag()
    {
        return $this->request()->getSession()->getFlashBag();
    }

    public function db()
    {
        return $this->container->get('database');
    }

    public function orm()
    {
        return $this->get('orm');
    }

    public function repo($name = null, $module = null)
    {
        return $this->get('orm')->repository()->get($name, $module);
    }

    public function entity($name = null, $module = null)
    {
        return $this->get('orm')->entity()->get($name, $module);
    }

    public function t($key)
    {
        return $this->container->get('translation')->get($key);
    }

    public function localisation()
    {
        return $this->get('localisationResolver')->get($this->request()->getLocale());
    }

    public function datetime($time = null)
    {
        if($time === null)
        {
            return $this->get('datetime');
        }
        else
        {
            return $this->get('datetime')->date($time);
        }
    }

    public function registry()
    {
        return $this->container->get('registry');
    }

    public function eventDispatcher()
    {
        return $this->container->get('eventDispatcher');
    }

    public function document()
    {
        return $this->container->get('document');
    }

    public function openSettings($name, $param = null)
    {
        if($name === 'user' && $param === null)
        {
            $param = $this->user()->getId();
        }

        return $this->container->get('settings')->open($name, $param);
    }

    public function callPlugins($category, $action, array $params = [])
    {
        return $this->container->get('package.plugin.manager')->callPlugins($category, $action, $params);
    }

    public function createUrl(/* poly... */)
    {
        return 'http://'.$this->request()->server->get('HTTP_HOST', 'localhost').$this->container->get('request')->getBasePath().'index.php?'.call_user_func_array([ $this, 'createQueryString' ], func_get_args());
    }

    public function openUserHistory($entity, $module = null)
    {
        if($module === null)
        {
            $module = $this->container->get('routing')->getRoute()->getModule();
        }

        $logger = $this->container->get('history.user.entitylog');
        $logger->setModule($module);
        $logger->setEntity($entity);

        // Default, on start, we retrieve data from model (pre-save data).
        $logger->storePreValues();

        return $logger;
    }

    public function assetter()
    {
        return $this->container->get('assetter');
    }

    public function acl($section, $entity, $group = null)
    {
        return $this->container->get('permission.acl')->open($section, $entity, $group);
    }

    public function createQueryString(/* poly... */)
    {
        $params = func_get_args();

        if($params === [])
        {
            return Route::generateStringFromParams([]);
        }
        else
        {
            if(is_array($params[0]))
            {
                return Route::generateStringFromParams($params[0]);
            }
            else
            {
                $newParams = [ 'mod' => $params[0] ];

                for($i=1; $i<=3; $i++)
                {
                    if(isset($params[$i]))
                    {
                        if(is_array($params[$i]))
                        {
                            $newParams = array_merge($newParams, $params[$i]);
                            break;
                        }
                        else
                        {
                            switch($i)
                            {
                                case 1: $newParams['cnt'] = $params[$i]; break;
                                case 2: $newParams['act'] = $params[$i]; break;
                            }
                        }
                    }
                }

                return Route::generateStringFromParams($newParams);
            }
        }
    }
}
