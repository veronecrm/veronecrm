<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Update;

use ZipArchive;
use Exception;
use SimpleXMLElement;
use System\DependencyInjection\Container;

class Factory
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create($package)
    {
        // First, we must unpack archive and parse update file.
        $destination = pathinfo($package, PATHINFO_DIRNAME).'/'.pathinfo($package, PATHINFO_FILENAME);

        if(is_dir($destination) === false)
        {
            mkdir($destination, 0777, true);
        }

        $zip = new ZipArchive;

        if(! $zip->open($package))
        {
            throw new Exception('Cannot open Update package. File is broken.');
        }

        if(! $zip->extractTo($destination))
        {
            throw new Exception('Cannot extract package archive. File is broken or destination directory does not exists or is full.');
        }

        $zip->close();

        // Now we search for XML update file.
        if(file_exists($destination.'/update.xml') === false)
        {
            throw new Exception('Cannot find update.xml file in package. This file is required to install package.');
        }

        // Now, parse update file.
        $xml = new SimpleXMLElement($destination.'/update.xml', 0, true);

        // Get attributes
        $attributes = [];

        foreach($xml->attributes() as $key => $val)
        {
            $attributes[$key] = $val;
        }

        // Now we check which type of package is it, and create installator object.
        if(isset($attributes['type']) == false)
        {
            throw new Exception('This package has not defined type of bundle that updates. Cannot install it.');
        }

        if($attributes['type'] == 'core')
        {
            $installator = new Core($destination, $xml);
        }
        elseif($attributes['type'] == 'module')
        {
            $installator = new Module($destination, $xml);
        }
        else
        {
            throw new Exception('Unsupported type of package. Cannot install it.');
        }

        $installator->setContainer($this->container);

        return $installator;
    }
}
