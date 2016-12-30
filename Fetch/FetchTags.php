<?php

namespace Statamic\Addons\Fetch;

use Statamic\Extend\Tags;
use Statamic\API\Collection;

class FetchTags extends Tags
{
    private $fetch;

    public function init()
    {
        $this->fetch = new Fetch($this->parameters);
    }

    /**
     * Handle `{{ fetch:[collection_name] }}` tags
     */
    public function __call($method, $args)
    {
        if ($name = explode(':', $this->tag)[1]) {
            if ($name == 'pages') {
                return $this->fetch->pages();                
            }

            return $this->fetch->collection($name);
        }
    }

    /**
     * Handle `{{ fetch collection|page|pages="*" }}` tags
     */
    public function index()
    {
        $types = collect(['collection', 'page', 'pages']);

        $type = $types->first(function ($index, $type) {
            return in_array($type, array_keys($this->parameters));
        });

        return $this->fetch->$type($this->parameters[$type]);
    }
}