<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Config;

class Config
{
    use \System\Utils\ArrayIndexTranslateTrait;

    protected $params = [];

    public static function fromFile($filepath)
    {
        if(! file_exists($filepath))
        {
            return new self;
        }

        $info = pathinfo($filepath);

        if(isset($info['extension']))
        {
            switch($info['extension'])
            {
                case 'php': return new Php($filepath); break;
                case 'ini': return new Ini($filepath); break;
            }
        }

        return new self;
    }

    public function all()
    {
        return $this->params;
    }

    public function has($name)
    {
        $function = create_function('$array', 'return isset($array'.$this->createIndex($name).');');

        return $function($this->params);
    }

    public function get($name)
    {
        return $this->getFromArray($name, $this->params);
    }

    public function set($name, $val)
    {
        $this->params = $this->setInArray($name, $val, $this->params);

        return $this;
    }
}
