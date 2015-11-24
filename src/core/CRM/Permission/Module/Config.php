<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Permission\Module;

class Config
{
    public function getConfig($module)
    {
        // If exists access file for module, we return it's details.
        $filepath = BASEPATH."/app/App/Module/{$module}/access.php";

        if(file_exists($filepath))
        {
            return $this->validateDetails(include $filepath);
        }

        return null;

        // Otherwise, we check if base config access file exists and return it's details.
        /*$filepath = BASEPATH."/core/CRM/Permission/base-access-config.php";

        if(file_exists($filepath))
        {
            return $this->validateDetails(include $filepath);
        }*/
    }

    public function validateDetails($data)
    {
        if(is_array($data) === false)
        {
            return [];
        }

        foreach($data as $i => $section)
        {
            if(isset($data[$i]['name']) === false)
            {
                $data[$i]['name'] = '';
            }

            if(isset($data[$i]['id']) === false)
            {
                $data[$i]['id'] = '';
            }

            if(isset($data[$i]['access']) === false || is_array($data[$i]['access']) === false)
            {
                $data[$i]['access'] = [];
            }

            foreach($data[$i]['access'] as $j => $access)
            {
                if(isset($data[$i]['access'][$j]['name']) === false)
                {
                    $data[$i]['access'][$j]['name'] = '';
                }

                if(isset($data[$i]['access'][$j]['id']) === false)
                {
                    $data[$i]['access'][$j]['id'] = '';
                }
            }
        }

        return $data;
    }
}
