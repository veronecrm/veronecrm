<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Locale;

class LocalisationResolver
{
    private $localisations = [];

    public function get($locale)
    {
        $locale = strtoupper($locale);

        if(isset($this->localisations[$locale]))
        {
            return $this->localisations[$locale];
        }

        $className = "Language\\{$locale}\\Localisation";

        return $this->localisations[$locale] = new $className;
    }
}
