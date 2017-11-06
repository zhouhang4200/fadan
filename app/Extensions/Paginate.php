<?php

namespace App\Extensions;

use Illuminate\Support\Facades\Request;

class Paginator
{
    public $query;
    public $total;
    public $perPage;
    public $pageName;
    public $currentPage;
    public $url;
    public $parameters;


    function __construct()
    {
        $this->query = null;
        $this->perPage = 15;
        $this->pageName = 'page';
        $this->total = 0;
        $this->currentPage = 1;
        $this->url = Request::url();
        $this->parameters = Request::query();
    }

    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function setPageName($pageName)
    {
        $this->pageName = $pageName;

        return $this;
    }

    public function setQuery($query)
    {
        $this->query = $query;
        $this->total = $this->getTotal($query);

        return $this;
    }

    public function setCurrentPage($page = 1)
    {
        $this->currentPage = $page;
        return $this;
    }

    public function getTotal($query = null)
    {
        if (is_null($query)) {
            return $this->total;
        }
        $bindings = $query->getBindings();

        $sql = $query->toSql();

        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        $sql = str_replace('\\', '\\\\', $sql);

//        $sql = \DB::connection()->getPdo()->quote($sql);

        $total = \DB::select(\DB::raw("select count(*) as total_count from ($sql) as count_table"));

        return $total[0]->total_count;
    }

    public function getData()
    {
        if (is_null($this->query)) {
            return [];
        }

        $skip = ($this->currentPage - 1) * $this->perPage;

        return $this->query->skip($skip)->take($this->perPage)->get();
    }

    public function links($url = null)
    {
        $pagesCount = ceil($this->total / $this->perPage);

        if ($pagesCount == 1 || $pagesCount == 0) {
            return '';
        }

        $ul = '<ul class="pagination">';
        $_ul = '</ul>';

        $li = '';

        $parameters = $this->parameters;

        //show previous
        if ($this->currentPage != 1) {
            $parameters[$this->pageName] = ($this->currentPage - 1);
            $url = $this->url . '?' . http_build_query($parameters, '', '&');

            $li .= '<li><a href="' . $url . '">&lt;</a></li>';
        }

        //show pages
        for ($i = 1; $i <= $pagesCount; $i++) {
            $active = $this->currentPage == $i ? 'active' : '';

            $parameters[$this->pageName] = $i;

            $url = $this->currentPage == $i ? '#' : ($this->url . '?' . http_build_query($parameters, '', '&'));

            $li .= '<li class="' . $active . '"><a href="' . $url . '">' . $i . '</a></li>';
        }

        //show next
        if ($this->currentPage != $pagesCount) {
            $parameters[$this->pageName] = ($this->currentPage + 1);

            $url = $this->url . '?' . http_build_query($parameters, '', '&');

            $li .= '<li><a href="' . $url . '">&gt;</a></li>';
        }

        $html = $ul . $li . $_ul;

        return $html;
    }
}