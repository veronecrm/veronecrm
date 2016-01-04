<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Routing;

use System\Http\Request;

class Routing
{
    private $request;
    private $route;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->route   = new Route;
    }

    public function resolve()
    {
        $this->route->setModule($this->request->query->get('mod', 'Home'));
        $this->route->setController($this->request->query->get('cnt', 'Home'));
        $this->route->setAction($this->request->query->get('act', 'index'));

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }
}
