<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Install;

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

    public function create($package)
    {
        // First, we must unpack archive and parse update file.
        $destination = BASEPATH.'/app/Cache/Package/Install/'.pathinfo($package, PATHINFO_FILENAME).'-'.date('d-m-y_H-i-s');

        if(is_dir($destination) === false)
        {
            mkdir($destination, 0777, true);
        }

        $zip = new ZipArchive;

        $status = $zip->open($package);

        // Check open status
        if($status !== true)
        {
            switch($status)
            {
                case ZipArchive::ER_EXISTS: throw new Exception('Can not open ZIP file: File already exists.');
                case ZipArchive::ER_INCONS: throw new Exception('Can not open ZIP file: Zip archive inconsistent.');
                case ZipArchive::ER_INVAL:  throw new Exception('Can not open ZIP file: Invalid argument.');
                case ZipArchive::ER_MEMORY: throw new Exception('Can not open ZIP file: Malloc failure.');
                case ZipArchive::ER_NOENT:  throw new Exception('Can not open ZIP file: No such file.');
                case ZipArchive::ER_NOZIP:  throw new Exception('Can not open ZIP file: Not a zip archive.');
                case ZipArchive::ER_OPEN:   throw new Exception('Can not open ZIP file: Can\'t open file.');
                case ZipArchive::ER_READ:   throw new Exception('Can not open ZIP file: Read error.');
                case ZipArchive::ER_SEEK:   throw new Exception('Can not open ZIP file: Seek error.');
                default: throw new Exception('Can not open ZIP file: Unknown error.');
            }
        }

        if(! $zip->extractTo($destination))
        {
            throw new Exception('Cannot extract package archive. File is broken or destination directory does not exists or is full.');
        }

        $zip->close();

        // Now we search for XML manifest file.
        if(file_exists($destination.'/manifest.xml') === false)
        {
            throw new Exception('Cannot find manifest.xml file in package. This file is required to install package.');
        }

        $manifest = new ManifestParser($destination.'/manifest.xml');

        if($manifest->getType() == 'module')
        {
            $installator = new Module($destination, $manifest);
        }
        else
        {
            throw new Exception('Unsupported type of package. Cannot install it.');
        }

        $installator->setContainer($this->container);

        return $installator;
    }
}
