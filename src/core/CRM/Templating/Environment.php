<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Templating;

use CRM\Base;
use DateTime;

class Environment extends \Atline\Atline\Environment
{
    protected $base;

    public function __construct(Base $base)
    {
        $this->base = $base;
    }

    public function t($definition)
    {
        return $this->base->t($definition);
    }

    public function createUrl(/* poly... */)
    {
        return call_user_func_array([$this->base, 'createUrl'], func_get_args());
    }

    public function asset($path)
    {
        return $this->base->request()->getUriForPath($path);
    }

    public function date($format, $timestamp = 'now')
    {
        return date($format, $timestamp);
    }
}
