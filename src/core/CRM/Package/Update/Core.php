<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Update;

class Core extends InstallatorAbstract
{
    public function prepare()
    {
        $this->destination = BASEPATH;
    }

    public function install()
    {
        if(! is_dir($this->destination))
        {
            mkdir($this->destination, 0777, true);
        }

        // Create directories if some not exists, and copy (with replace) files.
        foreach($this->getFiles() as $file)
        {
            // Create relative path of file/dir
            $path = $this->createRelativePath($file);

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

        $this->installSettings();
    }

    public function backupFiles($source, $destination)
    {
        if(! is_dir($destination))
        {
            mkdir($destination, 0777, true);
        }

        foreach($this->getFiles() as $file)
        {
            // Create relative path of file/dir
            $path = $this->createRelativePath($file);

            // Path to file/dir that should be existed and replaced in updating
            $newPath = $destination.$path;

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
}
