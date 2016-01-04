<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Module;

use System\DependencyInjection\Container;
use System\Database\Database;

class ModuleManager
{
    private $list = [];

    public function __construct(Container $container, Database $database)
    {
        foreach($database->query("SELECT * FROM #__package WHERE type = 'module'") as $row)
        {
            $module = new Module($row['uid'], $row['name']);
            $module->setContainer($container);
            $module->setVersion($row['version']);
            $module->setReleaseDate($row['releaseDate']);
            $module->setAuthorName($row['authorName']);
            $module->setAuthorUrl($row['authorUrl']);
            $module->setLicense($row['license']);

            $this->list[] = $module;
        }
    }

    public function all()
    {
        return $this->list;
    }

    public function exists($name)
    {
        return file_exists(BASEPATH."/app/App/Module/{$name}");
    }
}
