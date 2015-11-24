<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Utils;

use CRM\Base;

class Datetime extends Base
{
    public function getDateFormat()
    {
        return 'H:i, d.m.Y';
    }

    public function getShortDateFormat()
    {
        return 'd.m.Y';
    }

    public function dateShort($time)
    {
        return date($this->getShortDateFormat(), $time);
    }

    public function date($time)
    {
        return date($this->getDateFormat(), $time);
    }

    public function timeAgo($time)
    {
        if($time == 0)
        {
            return $this->t('never');
        }

        $periods = ["second", "minute", "hour"];
        $lengths = ["60", "60", "24"];

        $difference = time() - $time;

        /**
         * @todo If element was added 24 hours ago, we return "Yesterday".
         *       Must compute, if 00:00 hour was left (next day).
         */
        // 86400 == 24 hours
        /*if($difference >= 86400)
        {
            return 'yesterday';
        }*/

        for($i = 0; $difference >= $lengths[$i] && $i < count($lengths)-1; $i++)
        {
            $difference /= $lengths[$i];
        }

        $difference = round($difference);

        if($difference >= 24 && $periods[$i] == 'hour')
        {
            return $this->date($time);
        }

        return $difference.' '.$this->localisation()->dateVariety($difference, $periods[$i]).' '.mb_strtolower($this->t('ago'));
    }

    /**
     * Returns info, if given date is Today.
     * @param  integer  $date Date to check. Unix timestamp.
     * @return boolean
     */
    public function isToday($date)
    {
        return date('dmY') === date('dmY', $date);
    }
}
