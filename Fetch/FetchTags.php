<?php

namespace Statamic\Addons\Fetch;

use Statamic\Extend\Tags;

class FetchTags extends Tags
{
    private $fetch;

    /**
     * Handle `{{ fetch:[collection_name] }}` tags
     */
    public function __call($method, $args)
    {
        $this->fetch = app(Fetch::class);
        $this->fetch->setParameters($this->parameters);

        if ($name = explode(':', $this->tag)[1]) {
            if ($name === 'pages') {
                return $this->fetch->pages();
            }

            if ($name === 'globals') {
                return $this->fetch->globals();
            }

            return $this->fetch->collection($name);
        }
    }

    /**
     * Handle `{{ fetch collection|entry|page|pages="*"|global|globals="*" }}` tags
     */
    public function index()
    {
        $this->fetch = app(Fetch::class);
        $this->fetch->setParameters($this->parameters);

        $types = collect(['collection', 'entry', 'page', 'pages', 'global', 'globals']);

        $type = $types->first(function ($index, $type) {
            return in_array($type, array_keys($this->parameters));
        });

        return $this->fetch->$type($this->parameters[$type]);
    }
}
