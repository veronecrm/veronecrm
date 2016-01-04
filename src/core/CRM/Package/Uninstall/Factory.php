<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Uninstall;

use ZipArchive;
use Exception;
use System\DependencyInjection\Container;
use CRM\Package\ManifestParser;

class Factory
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create($directory)
    {
        if(is_dir($directory) === false)
        {
            throw new Exception('Package directory doesn\'t exists.');
        }

        // Now we search for XML manifest file.
        if(file_exists($directory.'/manifest.xml') === false)
        {
            throw new Exception('Cannot find manifest.xml file in package directory. This file is required to uninstall package.');
        }

        $manifest = new ManifestParser($directory.'/manifest.xml');

        if($manifest->getType() == 'module')
        {
            $uninstallator = new Module($directory, $manifest);
        }
        else
        {
            throw new Exception('Unsupported type of package. Cannot uninstall it.');
        }

        $uninstallator->setContainer($this->container);

        return $uninstallator;
    }
}
