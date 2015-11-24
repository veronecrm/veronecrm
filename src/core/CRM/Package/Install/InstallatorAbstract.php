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
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Symfony\Component\Filesystem\Filesystem;

abstract class InstallatorAbstract implements InstallatorInterface
{
    /**
     * Temporary directory where package files exists.
     * @var string
     */
    protected $temporaryDir;

    /**
     * Store manifest object.
     * @var ManifestParser
     */
    protected $manifest;

    /**
     * Store installation logs.
     * @var array
     */
    protected $logs = [];

    /**
     * {@inheritdoc}
     */
    abstract public function install();

    /**
     * {@inheritdoc}
     */
    abstract public function cleanUp();

    /**
     * {@inheritdoc}
     */
    public function getLogs($asArray = true)
    {
        return $asArray ? $this->logs : implode('<br />', $this->logs);
    }

    /**
     * {@inheritdoc}
     */
    public function getManifest()
    {
        return $this->manifest;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemporaryDir()
    {
        return $this->temporaryDir;
    }

    protected function log($status, $message)
    {
        $this->logs[] = "$status: $message";
    }

    protected function copyDirectory($source, $destination)
    {
        $contents = $this->getDirectoryContents($source);
        $fs       = new Filesystem();

        foreach($contents as $item)
        {
            if($item['type'] == 'dir')
            {
                $fs->mkdir($destination.$item['relative-path'], 0777);
            }
            else
            {
                $fs->copy($item['path'], $destination.$item['relative-path'], true);
            }
        }
    }

    protected function getDirectoryContents($directory)
    {
        $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD);
        $result = [ 'dirs' => [], 'files' => [] ];

        foreach($iter as $path => $item)
        {
            if($item->isDir())
            {
                $result['dirs'][] = [
                    'path' => $path,
                    'relative-path' => '/'.trim(str_replace($directory, '', $path), ' /'),
                    'type' => 'dir'
                ];
            }
            else
            {
                $result['files'][] = [
                    'path' => $path,
                    'relative-path' => '/'.trim(str_replace($directory, '', $path), ' /'),
                    'type' => 'file'
                ];
            }
        }

        return array_merge($result['dirs'], $result['files']);
    }
}
