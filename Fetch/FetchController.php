<?php

namespace Statamic\Addons\Fetch;

use Statamic\Extend\Controller;

class FetchController extends Controller
{
    private $fetch;

    public function __construct()
    {
        $this->fetch = new Fetch;

        if (! $this->fetch->auth) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }

    public function getNav()
    {
        return $this->fetch->nav();
    }

    public function getTaxonomy()
    {
        return $this->fetch->taxonomy();
    }

    public function postTaxonomy()
    {
        return $this->fetch->taxonomy();
    }

    public function getTaxonomies()
    {
        return $this->fetch->taxonomies();
    }

    public function postTaxonomies()
    {
        return $this->fetch->taxonomies();
    }

    public function getTerm()
    {
        return $this->fetch->term();
    }

    public function postTerm()
    {
        return $this->fetch->term();
    }

    public function getTerms()
    {
        return $this->fetch->terms();
    }

    public function postTerms()
    {
        return $this->fetch->terms();
    }

    public function getCollection()
    {
        return $this->fetch->collection();
    }

    public function postCollection()
    {
        return $this->fetch->collection();
    }

    public function getEntry()
    {
        return $this->fetch->entry();
    }

    public function postEntry()
    {
        return $this->fetch->entry();
    }

    public function getPage()
    {
        return $this->fetch->page();
    }

    public function postPage()
    {
        return $this->fetch->page();
    }

    public function getPages()
    {
        return $this->fetch->pages();
    }

    public function postPages()
    {
        return $this->fetch->pages();
    }

    public function getGlobal()
    {
        return $this->fetch->global();
    }

    public function postGlobal()
    {
        return $this->fetch->global();
    }

    public function getGlobals()
    {
        return $this->fetch->globals();
    }

    public function postGlobals()
    {
        return $this->fetch->globals();
    }

    public function getSearch()
    {
        return $this->fetch->search();
    }

    public function postSearch()
    {
        return $this->fetch->search();
    }

    public function postForm()
    {
        return $this->fetch->form();
    }
}
