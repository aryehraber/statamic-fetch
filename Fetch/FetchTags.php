<?php

namespace Statamic\Addons\Fetch;

use Statamic\Extend\Tags;
use Statamic\API\Collection;

class FetchTags extends Tags
{
    private $fetch;

    public function init()
    {
        $this->fetch = new Fetch;
    }

    /**
     * Handle all {{ fetch:* }} tags
     */
    public function __call($method, $args)
    {
        if ($name = explode(':', $this->tag)[1]) {
            return $this->handle($name);
        }
    }

    /**
     * Handle {{ fetch collection="*" }} tags
     */
    public function index()
    {
        $name = $this->getParam('collection');

        return $this->handle($name);
    }

    public function handle($name)
    {
        if (! $collection = Collection::whereHandle($name)) {
            return "Collection [$name] not found.";
        }

        return $this->fetch->handle($collection, $this->getParam('deep'), $this->getParam('debug'));
    }
}
