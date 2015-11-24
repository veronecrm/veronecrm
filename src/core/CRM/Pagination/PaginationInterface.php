<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Pagination;

interface PaginationInterface
{
    public function paginationCount();

    public function paginationGet($start, $limit);
}
