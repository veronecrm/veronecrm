<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\DependencyInjection;

use System\EventDispatcher\EventDispatcher;
use System\DependencyInjection\Container;

class ContainerBuilder
{
    private static $container;
    private static $dispatcher;

    public static function build(EventDispatcher $dispatcher)
    {
        self::$container  = new Container($dispatcher);
        self::$container->set('container', self::$container);
        self::$dispatcher = $dispatcher;

        $self = new self();
        $self->appendFromFile(BASEPATH.'/core/services.php');

        return self::$container;
    }

    public function appendFromFile($filename)
    {
        if(file_exists($filename))
        {
            $this->appendFromArray(include($filename));
        }
    }

    public function appendFromArray(array $services)
    {
        self::$container->registerFromArray($services);
    }
}
