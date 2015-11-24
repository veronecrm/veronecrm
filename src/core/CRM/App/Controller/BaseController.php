<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\App\Controller;

use System\Http\Response;
use System\Http\RedirectResponse;
use CRM\Base;

class BaseController extends Base
{
    public function response($content = '', $status = 200, $headers = [])
    {
        return new Response($content, $status, $headers);
    }

    public function responseAJAX(array $content)
    {
        return new Response(json_encode($content));
    }

    public function redirect(/* poly... */)
    {
        return new RedirectResponse($this->container->get('request')->getUriForPath('/?'.call_user_func_array([ $this, 'createQueryString' ], func_get_args())));
    }

    public function redirectToUrl($url, $status = 303)
    {
        return new RedirectResponse($url, $status);
    }

    public function render($definition = '', array $params = [])
    {
        return new Response($this->get('templating.engine')->render($definition, $params));
    }
}
