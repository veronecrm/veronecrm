<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Pagination;

class Paginator
{
    protected $model;

    protected $total;
    protected $page = 1;
    protected $perPage = 20;
    protected $showPages = 1;
    protected $activeLinkClassName = 'active';
    protected $paginationClassName = 'pagination pagination-right';
    protected $baseUrl;

    protected $textPrev = '<span aria-hidden="true">&laquo;</span>';
    protected $textNext = '<span aria-hidden="true">&raquo;</span>';

    public function __construct(PaginationInterface $model, $page, $baseUrl = null)
    {
        $this->model   = $model;
        $this->page    = $page;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Gets the model.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the $model.
     *
     * @param mixed $model the model
     *
     * @return self
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Gets the total.
     *
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Sets the $total.
     *
     * @param mixed $total the total
     *
     * @return self
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Gets the page.
     *
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Sets the $page.
     *
     * @param mixed $page the page
     *
     * @return self
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Gets the perPage.
     *
     * @return mixed
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Sets the $perPage.
     *
     * @param mixed $perPage the per page
     *
     * @return self
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Gets the showPages.
     *
     * @return mixed
     */
    public function getShowPages()
    {
        return $this->showPages;
    }

    /**
     * Sets the $showPages.
     *
     * @param mixed $showPages the show pages
     *
     * @return self
     */
    public function setShowPages($showPages)
    {
        $this->showPages = $showPages;

        return $this;
    }

    /**
     * Gets the activeLinkClassName.
     *
     * @return mixed
     */
    public function getActiveLinkClassName()
    {
        return $this->activeLinkClassName;
    }

    /**
     * Sets the $activeLinkClassName.
     *
     * @param mixed $activeLinkClassName the active link class name
     *
     * @return self
     */
    public function setActiveLinkClassName($activeLinkClassName)
    {
        $this->activeLinkClassName = $activeLinkClassName;

        return $this;
    }

    /**
     * Gets the paginationClassName.
     *
     * @return mixed
     */
    public function getPaginationClassName()
    {
        return $this->paginationClassName;
    }

    /**
     * Sets the $paginationClassName.
     *
     * @param mixed $paginationClassName the pagination class name
     *
     * @return self
     */
    public function setPaginationClassName($paginationClassName)
    {
        $this->paginationClassName = $paginationClassName;

        return $this;
    }

    /**
     * Gets the baseUrl.
     *
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets the $baseUrl.
     *
     * @param mixed $baseUrl the base url
     *
     * @return self
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Gets the textPrev.
     *
     * @return mixed
     */
    public function getTextPrev()
    {
        return $this->textPrev;
    }

    /**
     * Sets the $textPrev.
     *
     * @param mixed $textPrev the text prev
     *
     * @return self
     */
    public function setTextPrev($textPrev)
    {
        $this->textPrev = $textPrev;

        return $this;
    }

    /**
     * Gets the textNext.
     *
     * @return mixed
     */
    public function getTextNext()
    {
        return $this->textNext;
    }

    /**
     * Sets the $textNext.
     *
     * @param mixed $textNext the text next
     *
     * @return self
     */
    public function setTextNext($textNext)
    {
        $this->textNext = $textNext;

        return $this;
    }

    public function getStart()
    {
        $this->total = $this->model->paginationCount();

        if($this->page < 1)
        {
            $this->page = 1;
        }
        
        $page = $this->page - 1;
        
        if($this->page == 0)
        {
            return 0;
        }
        else
        {
            return ($this->perPage * $this->page) - $this->perPage;
        }
    }

    public function getLimit()
    {
        if($this->perPage < 1)
        {
            $this->perPage = 1;
        }
        
        return $this->perPage;
    }

    public function getElements()
    {
        return $this->model->paginationGet($this->getStart(), $this->getLimit());
    }

    public function __toString()
    {
        return $this->generate();
    }

    public function generate()
    {
        $this->total = $this->model->paginationCount();

        $baseUrl = $this->prepareBaseUrl();
        $links = array();
        $space = true;

        $pages = ceil(($this->total / $this->perPage));
        
        if($pages <= 1) return '';

        for($i = 1; $i <= $pages; $i++)
        {
            if($i == 1 || $i == $pages || ($i >= $this->page - $this->showPages && $i <= $this->page + $this->showPages))
            {
                $space = true;

                $links[$i]['url']  = str_replace('%7BPAGE%7D', $i, $baseUrl);
                $links[$i]['page'] = strval($i);
            }
            elseif($space == true)
            {
                $space = false;
                $links[$i]['page'] = "...";
            }
        }

        $result = '<nav><ul class="'.$this->paginationClassName.'"><li'.($this->page == 1 ? ' class="disabled"' : '').'><a href="'.str_replace('%7BPAGE%7D', 1, $baseUrl).'">'.$this->textPrev.'</a></li>';

        foreach($links as $link)
        {
            $result .= '<li class="'.($link['page'] == $this->page ? $this->activeLinkClassName : '').(! isset($link['url']) ? ' disabled' : '').'"><a href="'.(isset($link['url']) ? $link['url'] : '#').'">'.$link['page'].'</a></li>';
        }

        return $result.'<li'.($this->page == $pages ? ' class="disabled"' : '').'><a href="'.str_replace('%7BPAGE%7D', ($this->page == $pages ? $pages : $this->page + 1), $baseUrl).'">'.$this->textNext.'</a></li></ul></nav>';
    }

    public function prepareBaseUrl()
    {
        $sections = parse_url($this->baseUrl);

        if(isset($sections['query']))
        {
            parse_str($sections['query'], $sections['query']);
        }
        else
        {
            $sections['query'] = [];
        }

        $sections['query']['page'] = '{PAGE}';

        return "{$sections['scheme']}://{$sections['host']}{$sections['path']}?".http_build_query($sections['query']);
    }
}
