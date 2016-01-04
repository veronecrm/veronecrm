<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Http;

use System\ParameterBag;

class ServerBag extends ParameterBag
{
    public function getHeaders()
    {
        $headers = [];

        if(! function_exists('getallheaders') || ! ($headers = getallheaders()))
        {
            $contentHeaders = ['CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true];

            foreach($this->parameters as $key => $value)
            {
                if(strpos($key, 'HTTP_') === 0)
                {
                    $headers[substr($key, 5)] = $value;
                }
                // CONTENT_* are not prefixed with HTTP_
                elseif(isset($contentHeaders[$key]))
                {
                    $headers[$key] = $value;
                }
            }
        }

        return $headers;
    }
}
