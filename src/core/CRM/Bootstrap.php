<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM;

use System\DependencyInjection\Container;
use System\Database\Connector;
use System\Database\Database;
use System\Config\Config;
use System\Http\Session\Session;
use System\Http\Session\FlashBag;
use CRM\Settings\AppProvider;
use CRM\Settings\UserProvider;
use CRM\Settings\SessionCache;
use Atline\Atline\Engine;
use CRM\Templating\Environment;
use CRM\Templating\DefinitionResolver;
use CRM\Base;
use Assetter\Assetter\Assetter;

class Bootstrap
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function onAppStart()
    {
        $request    = $this->container->get('request');
        $dispatcher = $this->container->get('eventDispatcher');

        $this->container->set('database', new Database((new Connector())->connect(Config::fromFile(BASEPATH.'/db.php'))));

        $appProvider  = new AppProvider($this->container->get('database'));
        $userProvider = new UserProvider($this->container->get('database'));

        $dispatcher->addListener('onUserAdd', $userProvider);
        $dispatcher->addListener('onUserDelete', $userProvider);

        $settings = $this->container->get('settings');
        $settings->register('app', $appProvider);
        $settings->register('user', $userProvider);

        $session = new Session($this->container->get('database'), $request);
        $session->start();
        $session->setBag('flashes', new FlashBag);

        $settings->setCache(new SessionCache($session));
        $settings->cache()->retrieve();

        $request->setSession($session);

        if($request->query->has('locale'))
        {
            $request->setLocale($request->query->get('locale'));
        }
        elseif($session->has('locale'))
        {
            $request->setLocale($session->get('locale'));
        }
        else
        {
            $request->setLocale($settings->open('app')->get('language'));
        }

        $app = new Base;
        $app->setContainer($this->container);

        $engine = new Engine(BASEPATH.'/app/Cache/templating', new Environment($app));
        $engine->setDefinitionResolver(new DefinitionResolver($this->container->get('routing')->getRoute()));
        $engine->setDefaultExtends('master.base');
        $engine->setDefaultData([ 'app' => $app ]);
        $engine->setCached(false);

        $this->container->set('templating.engine', $engine);

        foreach($this->container->get('package.module.manager')->all() as $module)
        {
            $filename = $module->getRoot().'/services.php';

            if(file_exists($filename))
            {
                $this->container->registerFromArray(include($filename));
            }
        }

        $assetter = new Assetter(include BASEPATH.'/web/assetter.php', 0, 'body');
        $assetter->registerNamespace('{ROOT}', rtrim($request->getUriForPath('/'), ' /'));
        $assetter->registerNamespace('{ASSETS}', $request->getUriForPath('/assets'));

        $this->container->set('assetter', $assetter);

        if($session->isNewSession())
        {
            $dispatcher->dispatch('onSessionCreateNew');
        }
    }

    public function onAppClose()
    {
        $this->container->get('request')->getSession()->save();
        $this->container->get('settings')->cache()->save();
    }
}
