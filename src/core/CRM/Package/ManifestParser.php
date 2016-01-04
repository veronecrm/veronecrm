<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package;

use Exception;
use ZipArchive;
use SimpleXMLElement;

class ManifestParser
{
    /**
     * Source parsed XML object.
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * Type of package, stored in type attribute.
     * @var string
     */
    protected $type = '';

    /**
     * Indexes that can be used in get() method.
     * @var array
     */
    protected $textData = [
        'uid', 'name', 'version', 'relase-date',
        'license', 'author.name', 'author.url'
    ];

    public function __construct($filepath)
    {
        if(is_file($filepath) === false)
        {
            throw new \Exception(sprintf('Manifest file does not exists: %s', $filepath));
        }

        $this->xml = new SimpleXMLElement(file_get_contents($filepath));

        $attributes = $this->getAttributes($this->xml);

        $this->type = isset($attributes['type']) ? $attributes['type'] : '';
    }

    /**
     * Return type of package defined in manifest file.
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return only predefined meta-data values (as string) defined in $this->textData
     * property. If given $index does not exists in $this->textData array, method return null.
     * @param  string $index Index to search in XML. If we want to get sth like this:
     *                       $xml->some->inner->tag, we need pass: get('some.inner.tag')
     *                       and split every one XML property name with dot.
     * @return mixed         If index exists, and if is defined in $this->textData
     *                       return (converted to string) its value. Otherwise return null.
     */
    public function get($index)
    {
        if(in_array($index, $this->textData) === false)
        {
            return null;
        }

        $node = $this->getNodeFromIndex($index);

        if($node === null)
        {
            return null;
        }

        return (string) $node;
    }

    /**
     * Check if index in XML exists (physically).
     * @param  string  $index Index to check.
     * @return boolean
     */
    public function has($index)
    {
        return $this->getNodeFromIndex($index) === null ? false : true;
    }

    /**
     * Search for settings in XML and convert it to array (with tags attributes)
     * and returns that array.
     * @return array Array of settings.
     */
    public function getSettings()
    {
        $settings = [];
        $node     = $this->getNodeFromIndex('setting');

        if(! $node)
        {
            return [];
        }

        foreach($node as $setting)
        {
            $attrs = $this->getAttributes($setting);

            $settings[] = [
                'type' => isset($attrs['type']) ? $attrs['type'] : '0',
                'name' => isset($attrs['name']) ? $attrs['name'] : 'unknown-name',
                'value' => (string) $setting
            ];
        }

        return $settings;
    }

    /**
     * Search for DB Queries in XML and convert it to array (with tags attributes)
     * and returns that array.
     * @return array Array of settings.
     */
    public function getDBQueries()
    {
        $queries = [];
        $node    = $this->getNodeFromIndex('db-query');

        if(! $node)
        {
            return [];
        }

        foreach($node as $query)
        {
            $attrs = $this->getAttributes($query);

            $queries[] = [
                'type'     => isset($attrs['type']) ? $attrs['type'] : 'raw',
                'scenario' => isset($attrs['scenario']) ? $attrs['scenario'] : 'install',
                'value'    => (string) $query
            ];
        }

        return $queries;
    }

    /**
     * Return array of founded attributes of given $node.
     * @param  SimpleXmlElement $node Node to retrive attributes from.
     * @return array
     */
    public function getAttributes($node)
    {
        $attributes = [];

        foreach($node->attributes() as $name => $value)
        {
            $attributes[$name] = $value;
        }

        return $attributes;
    }

    protected function getNodeFromIndex($index)
    {
        $exploded = explode('.', $index);
        $current  = $this->xml;

        foreach($exploded as $name)
        {
            if(isset($current->{$name}))
            {
                $current = $current->{$name};
            }
            else
            {
                return null;
            }
        }

        return $current;
    }
}
