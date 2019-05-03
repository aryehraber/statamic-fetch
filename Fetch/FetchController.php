<?php

namespace Statamic\Addons\Fetch;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Statamic\Extend\Controller;

class FetchController extends Controller
{
    private $fetch;

    public function __construct(Fetch $fetch)
    {
        parent::__construct();

        $this->fetch = $fetch;
        $this->fetch->setParameters();

        if (! $this->fetch->auth) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }

    public function getCollection()
    {
        return $this->response($this->fetch->collection());
    }

    public function postCollection()
    {
        return $this->response($this->fetch->collection());
    }

    public function getEntry()
    {
        return $this->response($this->fetch->entry());
    }

    public function postEntry()
    {
        return $this->response($this->fetch->entry());
    }

    public function getPage()
    {
        return $this->response($this->fetch->page());
    }

    public function postPage()
    {
        return $this->response($this->fetch->page());
    }

    public function getPages()
    {
        return $this->response($this->fetch->pages());
    }

    public function postPages()
    {
        return $this->response($this->fetch->pages());
    }

    public function getGlobal()
    {
        return $this->response($this->fetch->global());
    }

    public function postGlobal()
    {
        return $this->response($this->fetch->global());
    }

    public function getGlobals()
    {
        return $this->response($this->fetch->globals());
    }

    public function postGlobals()
    {
        return $this->response($this->fetch->globals());
    }

    public function getSearch()
    {
        return $this->response($this->fetch->search());
    }

    public function postSearch()
    {
        return $this->response($this->fetch->search());
    }

    private function response($data) {
        if ($data instanceof Response) {
            $response = $data;
        } else {
            $fetchData = $data instanceof Collection ? $data->toArray() : [];
            $response = response()->json($fetchData);
        }

        $this->emitEvent('response.created', $response);

        return $response;
    }
}
