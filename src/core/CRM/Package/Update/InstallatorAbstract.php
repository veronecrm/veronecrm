<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Update;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

abstract class InstallatorAbstract
{
    protected $container;

    protected $source;
    protected $xml;
    protected $attributes = [];
    protected $type = 'core';
    protected $uid = '';
    protected $name = '';
    protected $version = '';
    protected $releaseDate = '';

    protected $destination;

    public function __construct($source, $xml)
    {
        $this->source = $source;
        $this->xml    = $xml;

        // Get attributes
        foreach($xml->attributes() as $key => $val)
        {
            $key = (string) $key;
            $val = (string) $val;
            $this->attributes[$key] = $val;

            switch($key)
            {
                case 'type' : $this->type = $val; break;
            }
        }

        $this->uid         = (string) $this->xml->uid;
        $this->name        = (string) $this->xml->name;
        $this->version     = (string) $this->xml->version;
        $this->releaseDate = (string) $this->xml->{'relase-date'};

        $this->prepare();
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getUID()
    {
        return $this->uid;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    public function getFiles($dir = null)
    {
        $dir    = $dir == null ? $this->source : $dir;
        $result = [];

        if(is_dir($dir) == false)
        {
            return $result;
        }

        $cdir   = scandir($dir);

        foreach($cdir as $key => $value)
        {
            if(! in_array($value, ['.', '..']))
            {
                $result[] = $dir.DIRECTORY_SEPARATOR.$value;

                if(is_dir($dir.DIRECTORY_SEPARATOR.$value))
                {
                    $result = array_merge($result, $this->getFiles($dir.DIRECTORY_SEPARATOR.$value));
                }
            }
        }

        return $result;
    }

    public function removeDirectory($pathToDirectory)
    {
        if(! is_writeable($pathToDirectory) && is_dir($pathToDirectory))
        {
            chmod($pathToDirectory, 0777);
        }

        if(! file_exists($pathToDirectory))
        {
            return false;
        }

        $handle = opendir($pathToDirectory);

        while($tmp = readdir($handle))
        {
            if($tmp != '..' && $tmp != '.' && $tmp != '')
            {
                if(is_writeable($pathToDirectory.'/'.$tmp) && is_file($pathToDirectory.'/'.$tmp))
                {
                    @unlink($pathToDirectory.'/'.$tmp);
                }
                elseif(! is_writeable($pathToDirectory.'/'.$tmp) && is_file($pathToDirectory.'/'.$tmp))
                {
                    chmod($pathToDirectory.'/'.$tmp, 0666);
                    @unlink($pathToDirectory.'/'.$tmp);
                }

                if(is_writeable($pathToDirectory.'/'.$tmp) && is_dir($pathToDirectory.'/'.$tmp))
                {
                    $this->removeDirectory($pathToDirectory.'/'.$tmp);
                }
                elseif(! is_writeable($pathToDirectory.'/'.$tmp) && is_dir($pathToDirectory.'/'.$tmp))
                {
                    chmod($pathToDirectory.'/'.$tmp, 0777);
                    $this->removeDirectory($pathToDirectory.'/'.$tmp);
                }
            }
        }

        closedir($handle);
        @rmdir($pathToDirectory);

        if(! is_dir($pathToDirectory))
            return true;
        else
            return false;
    }

    public function createRelativePath($path)
    {
        return str_replace($this->source, '', $path);
    }

    /**
     * Return boolean, if Install.php file exists. This file is an additionally
     * file that can be executed while installation.
     * @return boolean
     */
    public function installFileExists()
    {
        return file_exists($this->source.'/Install.php');
    }

    /**
     * Creates object of Install class.
     * @return object If class exists.
     * @return false  If class does not exists.
     */
    public function getInstallFileObject()
    {
        include_once $this->source.'/Install.php';

        if(class_exists('Install'))
        {
            return new \Install;
        }
        else
        {
            return false;
        }
    }

    /**
     * Install settings.
     * @return void
     */
    public function installSettings()
    {
        if(isset($this->xml->setting))
        {
            $settings = $this->container->get('settings');

            $userSettings = $settings->open('user');
            $appSettings  = $settings->open('app');

            foreach($this->xml->setting as $setting)
            {
                $attr = [];

                foreach($setting->attributes() as $key => $val)
                {
                    $attr[(string) $key] = (string) $val;
                }

                if(isset($attr['type']) && isset($attr['name']))
                {
                    if($attr['type'] == 0 && $appSettings->has($attr['name']) === false)
                    {
                        $appSettings->registerKey($attr['name'], (string) $setting);
                    }

                    if($attr['type'] == 2 && $appSettings->has($attr['name']) === false)
                    {
                        $userSettings->registerKey($attr['name'], (string) $setting);
                    }
                }
            }
        }
    }

    abstract public function backupFiles($source, $destination);

    abstract public function prepare();

    abstract public function install();

    abstract public function cleanUp();
}
