<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Translation;

use System\DependencyInjection\Container;
use System\Config\Config;

class Builder
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function onAppStart()
    {
        $locale      = $this->container->get('request')->getLocale();
        $localeUpper = strtoupper($locale);
        $translation = $this->container->get('translation');
        $translation->setLocale($locale);

        // Main language file
        if(file_exists(BASEPATH."/app/Language/{$localeUpper}/definitions.ini"))
        {
            $translation->attachFromConfig(Config::fromFile(BASEPATH."/app/Language/{$localeUpper}/definitions.ini"));
        }

        foreach($this->container->get('package.module.manager')->all() as $module)
        {
            $filename = BASEPATH."/app/App/Module/{$module->getName()}/Language/{$locale}.ini";

            if(file_exists($filename))
            {
                $translation->attachFromConfig(Config::fromFile($filename));
            }
        }
    }
}
