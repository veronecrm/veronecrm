<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Http;

class ResponseHeaderBag extends HeaderBag
{
    public function __construct(array $headers)
    {
        foreach($headers as $key => $values)
        {
            $this->set($key, $values);
        }
    }

    public function all()
    {
        $return = [];

        foreach($this->parameters as $key => $val)
        {
            $return[implode('-', array_map('ucfirst', explode('-', $key)))] = is_array($val) && $val ? $val[0] : $val;
        }

        return $return;
    }

    /**
     * @param  string $disposition 'inline' or 'attachment'
     * @param  string $filename
     * @param  string $filename2
     * @return string
     */
    public function setDisposition($disposition, $filename, $filename2 = '')
    {
        if($filename2 == '')
        {
            $filename2 = $filename;
        }

        $result = sprintf('%s; filename="%s"', $disposition, str_replace('"', '\\"', $filename2));

        if($filename !== $filename2)
        {
            $result .= sprintf("; filename*=utf-8''%s", rawurlencode($filename));
        }

        $this->set('Content-Disposition', $result);

        return $this;
    }
}
