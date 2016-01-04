<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Permission\Controller;

class Annotation
{
    protected $module;
    protected $controller;

    protected $reflection;

    public function __construct($module, $controller)
    {
        $this->module     = $module;
        $this->controller = $controller;

        if(class_exists("App\Module\\{$module}\Controller\\{$controller}"))
        {
            $this->reflection = new \ReflectionClass("App\Module\\{$module}\Controller\\{$controller}");
        }
    }

    /**
     * Gets annotation strings from Controller class and selected method, parses
     * for tags and results collected details about access for method.
     * 
     * @todo Cache for details generated for methods.
     * @param string $action Action method name (with Action suffix).
     * @return array Array of details.
     */
    public function collectDetails($action)
    {
        if($this->reflection === null)
        {
            return ['section' => '', 'access' => []];
        }

        $controllerAnnotation = $this->reflection->getDocComment();

        if($this->reflection->hasMethod($action.'Action'))
        {
            $actionAnnotation = $this->reflection->getMethod($action.'Action')->getDocComment();
        }
        else
        {
            $actionAnnotation = '';
        }

        $details = ['section' => 'mod.'.$this->module, 'access' => []];

        if($controllerAnnotation)
        {
            $data = $this->getTags($controllerAnnotation);
            $details['section'] = isset($data['section'][0]) ? $data['section'][0] : '';
        }

        if($actionAnnotation)
        {
            $data = $this->getTags($actionAnnotation);

            if(isset($data['section'][0]))
            {
                $data['section'] = $data['section'][0];
            }

            $details = array_merge($details, $data);
        }

        array_unshift($details['access'], 'core.module');

        return $details;
    }

    /**
     * Returns array of method annotation tags prefixed with @.
     * Work temporary for one-line tags-values.
     * 
     * @param  string $input Data to parse.
     * @return array  Array, where key is a tag name, and value is
     *                a array of tags values (one tag may have multiple
     *                values in multiple lines).
     */
    public function getTags($input)
    {
        preg_match_all('/@(.+)/', $input, $matches);

        $output = [];

        if(isset($matches[1][0]))
        {
            foreach($matches[1] as $match)
            {
                $expl = explode(' ', $match);

                $output[array_shift($expl)][] = trim(implode(' ', $expl));
            }
        }

        return $output;
    }
}
