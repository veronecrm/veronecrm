<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\DependencyInjection;

use System\EventDispatcher\EventDispatcher;

class Container
{
    private $services = [];
    private $objects  = [];
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function all()
    {
        return $this->services;
    }

    public function register($id, $className, array $arguments = [], array $listen = [], $useFactory = false, $alwaysNew = false)
    {
        $this->services[$id] = [
            'class'       => $className,
            'arguments'   => $arguments,
            'listen'      => $listen,
            'use-factory' => $useFactory,
            'always-new'  => $alwaysNew
        ];

        foreach($listen as $name)
        {
            $this->dispatcher->addListener($name, $this->get($id));
        }

        return $this;
    }

    public function registerFromArray(array $collection)
    {
        foreach($collection as $id => $service)
        {
            $serviceBuilder = $this->register(
                $id,
                (isset($service['class'])       ? $service['class']       : null),
                (isset($service['arguments'])   ? $service['arguments']   : []),
                (isset($service['listen'])      ? $service['listen']      : []),
                (isset($service['use-factory']) ? $service['use-factory'] : false),
                (isset($service['always-new'])  ? $service['always-new']  : false)
            );
        }
    }

    /**
     * Rejestracja serwisów z obiektu Config.
     * @todo ...
     * @param  Config $config [description]
     * @return [type]         [description]
     */
    public function registerFromConfig(Config $config)
    {
        
    }

    public function has($id)
    {
        return isset($this->services[$id]);
    }

    public function set($id, $object, array $arguments = [], array $listen = [])
    {
        $this->register($id, get_class($object), $arguments, $listen);

        $this->objects[$id] = $object;

        return $this;
    }

    public function get($id)
    {
        if(isset($this->services[$id]))
        {
            if(isset($this->objects[$id]) && $this->services[$id]['always-new'] === false)
            {
                return $this->objects[$id];
            }

            if($this->services[$id]['arguments'] != [])
            {
                $class = $this->createDependencies($this->services[$id]['class'], $this->services[$id]['arguments']);
            }
            else
            {
                if(class_exists($this->services[$id]['class'], true) === false)
                {
                    throw new \Exception('Service class "'.$this->services[$id]['class'].'" not found for "'.$id.'" service.');
                }

                $class = new $this->services[$id]['class'];
            }

            if($this->services[$id]['use-factory'])
            {
                $class->setContainer($this->get('container'));
            }

            return $this->objects[$id] = $class;
        }
        else
        {
            throw new \Exception('I can not find service named "'.$id.'"');
        }
    }

    private function createDependencies($className, array $parameters)
    {
        $params = [];

        foreach($parameters as $item)
        {
            $params[] = $this->get($item);
        }

        return (new \ReflectionClass($className))->newInstanceArgs($params);
    }
}
