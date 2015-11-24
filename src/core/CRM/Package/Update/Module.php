<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Update;

class Module extends InstallatorAbstract
{
    public function prepare()
    {
        $this->destination = BASEPATH.'/app/App/Module/'.$this->getName();
    }

    public function install()
    {
        if(! is_dir($this->destination))
        {
            mkdir($this->destination, 0777, true);
        }

        // Create directories if some not exists, and copy (with replace) files.
        foreach($this->getModuleFiles() as $file)
        {
            // Create relative path of file/dir
            $path = $this->createRelativePath($file, 'Module');

            // Path to file/dir that should be existed and replaced in updating
            $newPath = $this->destination.$path;

            if(is_dir($file) && ! is_dir($newPath))
            {
                mkdir($newPath, 0777, true);
            }
            elseif(is_file($file))
            {
                file_put_contents($newPath, file_get_contents($file));
            }
        }

        // Create directories if some not exists, and copy (with replace) files.
        foreach($this->getResourcesFiles() as $file)
        {
            // Create relative path of file/dir
            $path = $this->createRelativePath($file, 'Resources');

            // Path to file/dir that should be existed and replaced in updating
            $newPath = BASEPATH.'/web/modules/'.$this->getName().$path;

            if(is_dir($file) && ! is_dir($newPath))
            {
                mkdir($newPath, 0777, true);
            }
            elseif(is_file($file))
            {
                file_put_contents($newPath, file_get_contents($file));
            }
        }

        $this->installSettings();
    }

    public function backupFiles($source, $destination)
    {
        if(! is_dir($destination))
        {
            mkdir($destination, 0777, true);
        }

        foreach($this->getModuleFiles() as $file)
        {
            // Create relative path of file/dir
            $path = $this->createRelativePath($file, 'Module');

            // Path to file/dir that should be existed and replaced in updating
            $newPath = $destination.'/Module'.$path;

            // Path to file we backup
            $file    = $source.$path;

            if(is_dir($file) && ! is_dir($newPath))
            {
                mkdir($newPath, 0777, true);
            }
            elseif(is_file($file))
            {
                file_put_contents($newPath, file_get_contents($file));
            }
        }

        foreach($this->getResourcesFiles() as $file)
        {
            // Create relative path of file/dir
            $path = $this->createRelativePath($file, 'Resources');

            // Path to file/dir that should be existed and replaced in updating
            $newPath = $destination.'/Resources'.$path;

            // Path to file we backup
            $file    = $source.$path;

            if(is_dir($file) && ! is_dir($newPath))
            {
                mkdir($newPath, 0777, true);
            }
            elseif(is_file($file))
            {
                file_put_contents($newPath, file_get_contents($file));
            }
        }
    }

    public function cleanUp()
    {
        if(is_file(BASEPATH.'/Install.php'))
        {
            unlink(BASEPATH.'/Install.php');
        }

        if(is_file(BASEPATH.'/update.xml'))
        {
            unlink(BASEPATH.'/update.xml');
        }

        if(is_dir($this->source))
        {
            $this->removeDirectory($this->source);
        }
    }

    public function createRelativePath($path, $type = '')
    {
        return str_replace($this->source.'/'.$type, '', $path);
    }

    public function getModuleFiles()
    {
        return $this->getFiles($this->source.'/Module');
    }

    public function getResourcesFiles()
    {
        return $this->getFiles($this->source.'/Resources');
    }
}
