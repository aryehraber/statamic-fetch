<?php

namespace Statamic\Addons\Fetch;

use Statamic\API\Collection;
use Statamic\Extend\Controller;

class FetchController extends Controller
{
    private $fetch;

    public function init()
    {
        $this->fetch = new Fetch;
    }

    public function getCollection()
    {
        $name = request()->segment(4);

        if (! $collection = Collection::whereHandle($name)) {
            return "Collection [$name] not found.";
        }

        return $this->fetch->handle($collection, request('deep'), request('debug'));
    }
}