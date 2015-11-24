<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace Language\EN;

use System\Locale\LocalisationInterface;

class Localisation implements LocalisationInterface
{
    /**
     * {@inheritdoc}
     */
    public function dateVariety($value, $type)
    {
        switch($type)
        {
            case 'second': return $value == 1 ? 'second' : 'seconds'; break;
            case 'minute': return $value == 1 ? 'minute' : 'minutes'; break;
            case 'hour':   return $value == 1 ? 'hour' : 'hours'; break;
            case 'day':    return $value == 1 ? 'day' : 'days'; break;
            case 'week':   return $value == 1 ? 'week' : 'weeks'; break;
            case 'month':  return $value == 1 ? 'month' : 'months'; break;
            case 'year':   return $value == 1 ? 'year' : 'years'; break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function alphabet($uppercase = true)
    {
        if($uppercase)
        {
            return ['A', 'B', 'C', 'E', 'D', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        }
        else
        {
            return ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function monthName($number, $type = 1)
    {
        switch($number)
        {
            case 1: return $type == 1 ? 'January'   : 'Jan'; break;
            case 2: return $type == 1 ? 'February'  : 'Feb'; break;
            case 3: return $type == 1 ? 'March'     : 'Mar'; break;
            case 4: return $type == 1 ? 'April'     : 'Apr'; break;
            case 5: return $type == 1 ? 'May'       : 'May'; break;
            case 6: return $type == 1 ? 'June'      : 'Jun'; break;
            case 7: return $type == 1 ? 'July'      : 'Jul'; break;
            case 8: return $type == 1 ? 'August'    : 'Aug'; break;
            case 9: return $type == 1 ? 'September' : 'Sep'; break;
            case 10: return $type == 1 ? 'October'  : 'Oct'; break;
            case 11: return $type == 1 ? 'Novebmer' : 'Nov'; break;
            case 12: return $type == 1 ? 'December' : 'Dec'; break;
            default: return '---';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function monthsNames($type = 1)
    {
        if($type === 1)
        {
            return ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'Novebmer', 'December'];
        }
        else
        {
            return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        }
    }
}
