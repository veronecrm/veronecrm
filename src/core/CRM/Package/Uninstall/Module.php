<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Uninstall;

use Exception;
use ZipArchive;
use Symfony\Component\Filesystem\Filesystem;
use System\Database\Database;
use System\Settings\Settings;
use System\DependencyInjection\Container;
use CRM\Package\ManifestParser;

class Module extends UninstallatorAbstract
{
    protected $container;
    protected $database;
    protected $settings;

    public function __construct($directory, ManifestParser $manifest)
    {
        $this->packageDir = $directory;
        $this->manifest   = $manifest;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
        $this->database  = $this->container->get('database');
        $this->settings  = $this->container->get('settings');
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
        $this->log('info', 'Module::uninstall()');

        $fs = new Filesystem();

        /**
         * Check if module already exists.
         */
        $count = $this->database->builder()
            ->count('id')
            ->from('#__package')
            ->where('uid', $this->manifest->get('uid'))
            ->where('name', $this->manifest->get('name'))
            ->where('type', 'module')
            ->one();

        if(isset($count->count) && $count->count == 0)
        {
            $this->log('error', 'Module with this UID and Name doesn\'t exists.');

            throw new Exception('Module with this UID and Name doesn\'t exists. Cannot uninstall module again.');
        }

        $moduleName = $this->manifest->get('name');

        if(! $moduleName)
        {
            $this->log('error', 'Module has not defined Name.');

            throw new Exception('Module has not defined Name. Is required to uninstall module.');
        }

        /**
         * Uninstall from DB
         */
        $this->database->builder()
            ->where('uid', $this->manifest->get('uid'))
            ->where('name', $this->manifest->get('name'))
            ->delete('#__package');

        $this->log('info', 'Module removed from Database "modules" table.');

        /**
         * Execute SQL Queries.
         */
        if($this->manifest->has('db-query'))
        {
            foreach($this->manifest->getDBQueries() as $query)
            {
                if($query['scenario'] == 'uninstall')
                {
                    if($query['type'] == 'file')
                    {
                        if($fs->exists($this->packageDir.'/'.$query['value']))
                        {
                            $this->log('info', 'Executing query from file: '.$query['value']);

                            $this->database->exec(file_get_contents($this->packageDir.'/'.$query['value']));
                        }
                        else
                        {
                            $this->log('error', 'Executing query file "'.$query['value'].'" does not exists.');
                        }
                    }
                    elseif($query['type'] == 'raw')
                    {
                        $this->log('info', 'Executing raw query: '.$query['value']);

                        $this->database->exec($query['value']);
                    }
                    else
                    {
                        $this->log('error', 'Unknown db-query type. Given: '.$query['type']);
                    }
                }
            }
        }

        /**
         * Call Uninstall.php file.
         */
        if($fs->exists($this->packageDir.'/Uninstall.php') === true)
        {
            include $this->packageDir.'/Uninstall.php';

            if(class_exists('Uninstall'))
            {
                $this->log('info', 'Uninstall class exists.');

                $uninstall = new \Uninstall;

                if(method_exists($uninstall, 'setContainer'))
                {
                    $this->log('info', 'Uninstall::setContainer() method exists.');

                    $uninstall->setContainer($this->container);
                }

                $uninstall->doInstallation();

                $this->log('info', 'Uninstall::doInstallation() method called.');
            }
        }

        /**
         * Remove module files.
         */
        if($fs->exists($this->packageDir) === false)
        {
            $this->log('error', 'Can not find "Module" directory in given path.');

            throw new Exception('"Module" directory does not exists in uninstallation directory. Is required to uninstall module.');
        }

        $fs->remove($this->getDirectoryContentsSimple($this->packageDir));
        $fs->remove($this->packageDir);

        $this->log('info', '"Module" directory removed.');

        /**
         * Remove Resources of module.
         */
        if($fs->exists(BASEPATH.'/web/modules/'.$moduleName) === true)
        {
            $fs->remove($this->getDirectoryContentsSimple(BASEPATH.'/web/modules/'.$moduleName));
            $fs->remove(BASEPATH.'/web/modules/'.$moduleName);

            $this->log('info', '"Resources" directory removed.');
        }

        $this->cleanUp();

        $this->log('success', 'Module uninstalled.');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function cleanUp()
    {
        $this->log('info', 'Installation cleaned up.');
    }
}
