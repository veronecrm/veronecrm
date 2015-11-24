<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Install;

use Exception;
use ZipArchive;
use Symfony\Component\Filesystem\Filesystem;
use System\Database\Database;
use System\Settings\Settings;
use System\DependencyInjection\Container;
use CRM\Package\ManifestParser;

class Module extends InstallatorAbstract
{
    protected $container;
    protected $database;
    protected $settings;

    public function __construct($directory, ManifestParser $manifest)
    {
        $this->temporaryDir = $directory;
        $this->manifest     = $manifest;
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
    public function install()
    {
        $this->log('info', 'Module::install()');

        $fs = new Filesystem();

        if($fs->exists($this->temporaryDir) === false)
        {
            $this->log('error', 'Module source installation directory does not exists.');

            throw new Exception('Module source installation directory does not exists.');
        }

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

        if(isset($count->count) && $count->count != 0)
        {
            $this->log('error', 'Module with this UID and Name already exists.');

            throw new Exception('Module with this UID and Name already exists. Cannot install module again.');
        }

        $moduleName = $this->manifest->get('name');

        if(! $moduleName)
        {
            $this->log('error', 'Module has not defined Name.');

            throw new Exception('Module has not defined Name. Is required to install module.');
        }

        /**
         * Move module files.
         */
        if($fs->exists($this->temporaryDir.'/Module') === false)
        {
            $this->log('error', 'Can not find "Module" directory in given path.');

            throw new Exception('"Module" directory does not exists in installation package. Is required to install module.');
        }

        $this->copyDirectory($this->temporaryDir.'/Module', BASEPATH.'/app/App/Module/'.$moduleName);

        $this->log('info', '"Module" directory copied to destination.');

        /**
         * Move Resources of module.
         */
        if($fs->exists($this->temporaryDir.'/Resources') === true)
        {
            $this->copyDirectory($this->temporaryDir.'/Resources', BASEPATH.'/web/modules/'.$moduleName);

            $this->log('info', '"Resources" directory copied to destination.');
        }

        // Move manifext.xml file to module directory.
        copy($this->temporaryDir.'/manifest.xml', BASEPATH.'/app/App/Module/'.$moduleName.'/manifest.xml');

        /**
         * Install in DB
         */
        $this->database->builder()->insert('#__package', [
            'uid'       => $this->manifest->get('uid'),
            'name'      => $this->manifest->get('name'),
            'version'   => $this->manifest->get('version'),
            'releaseDate' => $this->manifest->get('relase-date'),
            'authorName'  => $this->manifest->get('author.name'),
            'authorUrl' => $this->manifest->get('author.url'),
            'license'   => $this->manifest->get('license'),
            'type'      => 'module'
        ]);

        $this->log('info', 'Module saved in Database "modules" table.');

        /**
         * Install settings.
         */
        if($this->manifest->has('setting'))
        {
            $settingsApp  = $this->settings->open('app');
            $settingsUser = $this->settings->open('user');

            foreach($this->manifest->getSettings() as $setting)
            {
                if($setting['type'] == 0)
                {
                    $this->log('info', 'Registeration of App setting key: '.$setting['name']);

                    $settingsApp->registerKey($setting['name'], $setting['value']);
                }
                elseif($setting['type'] == 2)
                {
                    $this->log('info', 'Registeration of User setting key: '.$setting['name']);

                    $settingsUser->registerKey($setting['name'], $setting['value']);
                }
            }
        }

        /**
         * Execute SQL Queries.
         */
        if($this->manifest->has('db-query'))
        {
            foreach($this->manifest->getDBQueries() as $query)
            {
                if($query['scenario'] == 'install')
                {
                    if($query['type'] == 'file')
                    {
                        if($fs->exists($this->temporaryDir.'/'.$query['value']))
                        {
                            $this->log('info', 'Executing query from file: '.$query['value']);

                            $this->database->exec(file_get_contents($this->temporaryDir.'/'.$query['value']));
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
         * Call Install.php file.
         */
        if($fs->exists($this->temporaryDir.'/Install.php') === true)
        {
            include $this->temporaryDir.'/Install.php';

            if(class_exists('Install'))
            {
                $this->log('info', 'Install class exists.');

                $install = new \Install;

                if(method_exists($install, 'setContainer'))
                {
                    $this->log('info', 'Install::setContainer() method exists.');

                    $install->setContainer($this->container);
                }

                $install->doInstallation();

                $this->log('info', 'Install::doInstallation() method called.');
            }
        }

        $this->cleanUp();

        $this->log('success', 'Module installed.');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function cleanUp()
    {
        $contents = $this->getDirectoryContents($this->temporaryDir);
        $fs       = new Filesystem();

        foreach($contents as $item)
        {
            $fs->remove($item['path']);
        }

        $fs->remove($this->temporaryDir);

        $this->log('info', 'Installation cleaned up.');
    }
}
