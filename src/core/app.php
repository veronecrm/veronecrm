<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

use EEHandler\EEHandler\EEHandler;
use System\Routing\Routing;
use System\EventDispatcher\EventDispatcher;
use System\Http\Request;
use CRM\Kernel\ControllerResolver;
use CRM\DependencyInjection\ContainerBuilder;

(new EEHandler(ENVIRONMENT))->register();

$request = new Request;

$dispatcher = new EventDispatcher();

$routing = new Routing($request);
$routing->resolve();

$container = ContainerBuilder::build($dispatcher);
$container->set('eventDispatcher', $dispatcher);
$container->set('request', $request);
$container->set('routing', $routing);
$container->set('classLoader', $loader);

$dispatcher = $container->get('eventDispatcher');
$dispatcher->dispatch('onAppStart');

$response = (new ControllerResolver($routing->getRoute(), $container))->call();
$response->headers
    ->set('X-Frame-Options', 'deny')
    ->set('X-Content-Type-Options', 'nosniff')
    ->set('X-XSS-Protection', '1; mode=block');

$container->set('response', $response);

$dispatcher->dispatch('onBeforeResponseSend', [ $response ]);
$response->send();
$dispatcher->dispatch('onAfterResponseSend', [ $response ]);
$dispatcher->dispatch('onAppClose');
