<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Locale;

interface LocalisationInterface
{
    /**
     * Return representation of ammount of given time value.
     * @param  integer $value
     * @param  string  $type  Type of interval. Possible values:
     *                        second, minute, hour, day, week, month, year
     * @return string
     */
    public function dateVariety($value, $type);

    /**
     * Return array of alphabet letters for localisation.
     * @param  boolean $uppercase The letters have to be uppercase?
     *                            Otherwise will be lowercase.
     * @return array              Array of alphabet letters.
     */
    public function alphabet($uppercase = true);

    /**
     * Return name of month (number of month in first argument). Second argument
     * allows define which type of name should be returned.
     * @param  integer  $number Month number (indexed from 1).
     * @param  integer  $type   Type of name to return.
     *                          1 - Full name:  January, July, November
     *                          2 - Short name: Jan, Jul, Nov
     * @return string Month name.
     */
    public function monthName($number, $type = 1);

    /**
     * Return names of all months. Argument allows define which
     * type of name should be returned.
     * @param  integer  $type   Type of name to return.
     *                          1 - Full name:  January, July, November
     *                          2 - Short name: Jan, Jul, Nov
     * @return array Months names.
     */
    public function monthsNames($type = 1);
}
