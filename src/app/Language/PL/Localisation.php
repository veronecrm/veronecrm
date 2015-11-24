<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace Language\PL;

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
            case 'second': return $this->varietyByDigit($value, 'sekunda', 'sekundy', 'sekund'); break;
            case 'minute': return $this->varietyByDigit($value, 'minuta', 'minuty', 'minut'); break;
            case 'hour':   return $this->varietyByDigit($value, 'godzina', 'godziny', 'godzin'); break;
            case 'day':    return $this->varietyByDigit($value, 'dzień', 'dni', 'dni'); break;
            case 'week':   return $this->varietyByDigit($value, 'tydzień', 'tygodnie', 'tygodni'); break;
            case 'month':  return $this->varietyByDigit($value, 'miesiąc', 'miesiące', 'miesięcy'); break;
            case 'year':   return $this->varietyByDigit($value, 'rok', 'lat', 'lat'); break;
        }
    }

    private function varietyByDigit($value, $var1, $var2, $var3)
    {
        if($value < 10)
        {
            if($value == 0 || $value >= 5 && $value <= 9) 
            {
                return $var3;
            }
            elseif($value == 1)
            {
                return $var1;
            }
            elseif($value >= 2 || $value <= 4)
            {
                return $var2;
            }
        }
        elseif($value >10 && $value <= 20)
        {
            return $var3;
        } 
        else
        {
            $lastDigit = substr($value, -1);
            
            if($lastDigit == 0 || $lastDigit == 1 || $lastDigit >=5 && $lastDigit <=9)
            {
                return $var3;
            }
            elseif($lastDigit >=2 && $lastDigit <=4)
            {
                return $var2;
            }
        }
        
        return $var1;
    }

    /**
     * {@inheritdoc}
     */
    public function alphabet($uppercase = true)
    {
        if($uppercase)
        {
            return ['A', 'Ą', 'B', 'C', 'Ć', 'D', 'E', 'Ę', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'Ł', 'M', 'N', 'Ń', 'O', 'Ó', 'P', 'Q', 'R', 'S', 'Ś', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ż', 'Ź'];
        }
        else
        {
            return ['a', 'ą', 'b', 'c', 'ć', 'd', 'e', 'ę', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'ł', 'm', 'n', 'ń', 'o', 'ó', 'p', 'q', 'r', 's', 'ś', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ż', 'ź'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function monthName($number, $type = 1)
    {
        switch($number)
        {
            case 1: return $type == 1 ? 'Styczeń'   : 'Sty'; break;
            case 2: return $type == 1 ? 'Luty'      : 'Lut'; break;
            case 3: return $type == 1 ? 'Marzec'    : 'Mar'; break;
            case 4: return $type == 1 ? 'Kwiecień'  : 'Kwi'; break;
            case 5: return $type == 1 ? 'Maj'       : 'Maj'; break;
            case 6: return $type == 1 ? 'Czerwiec'  : 'Cze'; break;
            case 7: return $type == 1 ? 'Lipiec'    : 'Lip'; break;
            case 8: return $type == 1 ? 'Sierpień'  : 'Sie'; break;
            case 9: return $type == 1 ? 'Wrzesień'  : 'Wrz'; break;
            case 10: return $type == 1 ? 'Październik' : 'Paź'; break;
            case 11: return $type == 1 ? 'Listopad' : 'Lis'; break;
            case 12: return $type == 1 ? 'Grudzień' : 'Gru'; break;
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
            return ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'];
        }
        else
        {
            return ['Sty', 'Lut', 'Mar', 'Kwi', 'Maj', 'Cze', 'Lip', 'Sie', 'Wrz', 'Paź', 'Lis', 'Gru'];
        }
    }
}
