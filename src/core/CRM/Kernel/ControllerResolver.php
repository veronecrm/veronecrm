<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Kernel;

use System\Routing\Route;
use System\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\GenericEvent;
use CRM\App\NotFoundException;

class ControllerResolver
{
    private $route;
    private $contianer;

    public function __construct(Route $route, Container $contianer)
    {
        $this->route     = $route;
        $this->contianer = $contianer;
    }

    public function call()
    {
        $this->contianer->get('eventDispatcher')->dispatch('onBeforeController', [ $this->route ]);

        $className = 'App\\Module\\'.$this->route->getModule().'\\Controller\\'.$this->route->getController();

        if(! class_exists($className))
        {
            return $this->terminateNotFound(sprintf('Class %s not exists.', $className));
        }

        $controller = new $className;

        if(! method_exists($controller, $this->route->getAction().'Action'))
        {
            return $this->terminateNotFound(sprintf('Action %s not exists.', $className.'::'.$this->route->getAction().'Action'));
        }

        if(method_exists($controller, 'setContainer'))
        {
            $controller->setContainer($this->contianer);
        }

        if(method_exists($controller, 'onBefore'))
        {
            $controller->onBefore();
        }

        try
        {
            $response = $controller->{$this->route->getAction().'Action'}($this->contianer->get('request'));
        }
        catch(NotFoundException $e)
        {
            return $this->terminateNotFound($e->getMessage());
        }
        catch(\Exception $e)
        {
            throw $e;
        }

        if(method_exists($controller, 'onAfter'))
        {
            $controller->onAfter();
        }

        $this->contianer->get('eventDispatcher')->dispatch('onAfterController', [ $this->route, $response ]);

        return $response;
    }

    public function terminateNotFound($message)
    {
        $controller = new \App\Module\Error\Controller\NotFound;
        $controller->setContainer($this->contianer);
        $this->contianer->get('request')->request->set('message', $message);
        $response = $controller->indexAction($this->contianer->get('request'));

        $this->contianer->get('eventDispatcher')->dispatch('onAfterController', [ $this->route, $response ]);

        return $response;
    }
}
