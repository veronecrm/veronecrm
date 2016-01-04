<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Module;

use System\DependencyInjection\Container;

class Module
{
    protected $container;
    protected $uid;
    protected $name;
    protected $version;
    protected $releaseDate;
    protected $authorName;
    protected $authorUrl;
    protected $license;

    public function __construct($uid, $name)
    {
        $this->uid  = $uid;
        $this->name = $name;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
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

    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getAuthorName()
    {
        return $this->authorName;
    }

    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getAuthorUrl()
    {
        return $this->authorUrl;
    }

    public function setAuthorUrl($authorUrl)
    {
        $this->authorUrl = $authorUrl;

        return $this;
    }

    public function getLicense()
    {
        return $this->license;
    }

    public function setLicense($license)
    {
        $this->license = $license;

        return $this;
    }

    public function getNameLocale()
    {
        return $this->container->get('translation')->get('modName'.$this->name);
    }

    public function getRoot()
    {
        return BASEPATH."/app/App/Module/{$this->getName()}";
    }
}
