<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace Helper;

use CRM\Base;

class Tax extends Base
{
    public function all()
    {
        return $this->db()->builder()->select('*')->from('#__tax')->orderBy('name', 'ASC')->all();
    }

    public function get($taxId)
    {
        return $this->db()->builder()->select('*')->from('#__tax')->where('id', $taxId)->one();
    }

    public function calculateGrossPrice($priceNet, $taxId)
    {
        $tax = $this->get($taxId);

        if(! $tax)
        {
            throw new \Exception('Tax with id '.$taxId.' does not exists.');
        }

        return (($tax->rate / 100) + 1) * $priceNet;
    }
}
