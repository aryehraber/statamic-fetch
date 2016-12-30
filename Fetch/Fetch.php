<?php

namespace Statamic\Addons\Fetch;

use Statamic\API\Str;
use Statamic\API\URL;
use Statamic\API\Page;
use Statamic\API\Asset;
use Statamic\API\Content;
use Statamic\API\Collection;
use Statamic\Extend\Extensible;
use Statamic\Assets\Asset as Image;
use Statamic\Data\Pages\Page as PageData;

class Fetch
{
    use Extensible;

    public $auth;
    public $deep;
    public $debug;

    public function __construct($params = null)
    {
        $params = collect($params);

        $this->auth = (new FetchAuth)->isAuth();
        $this->deep = request('deep') || $this->getConfigBool('deep') || $params->get('deep');
        $this->debug = request('debug') || $params->get('debug');
    }

    /**
     * Fetch collection
     */
    public function collection($name = null)
    {
        $name = $name ?: request()->segment(4);

        if (! $collection = Collection::whereHandle($name)) {
            return "Collection [$name] not found.";
        }

        return $this->handle($collection->entries());
    }

    /**
     * Fetch single page
     */
    public function page($uri = null)
    {
        $uri = $uri ?: request()->segment(4);

        if (! $uri || $uri == 'home') {
            $page = Page::whereUri('/');
        } else {
            if (strpos('/'.request()->path(), $this->actionUrl('page')) !== false) {
                $uri = explode(ltrim($this->actionUrl('page'), '/'), request()->path())[1];
            }

            $uri = Str::ensureLeft(trim($uri), '/');

            if (! $page = Page::whereUri($uri)) {
                return "Page [$uri] not found.";
            }
        }

        return $this->handle($page);
    }

    /**
     * Fetch multiple pages
     */
    public function pages($pages = null)
    {
        $pages = $pages ?: request('pages');

        if (! is_null($pages) && ! is_array($pages)) {
            $pages = explode(',', $pages);
        }

        if ($pages) {
            $pages = collect($pages)->map(function ($uri) {
                $uri = Str::ensureLeft(trim($uri), '/');
                return Page::whereUri($uri);
            })->filter();
        } else {
            $pages = Page::all();
        }

        return $this->handle($pages);
    }

    /**
     * Handle data
     */
    private function handle($data)
    {
        if ($this->deep) {
            if ($data instanceof PageData) {
                $data = $this->goDeep($data);
            }

            $data = $data->map(function ($item) {
                return $this->goDeep($item);
            });
        }

        if ($this->debug) {
            dd($data);
        }

        return $data;
    }

    /**
     * Fetch item data recursively
     */
    private function goDeep($item)
    {
        return collect($item)->map(function ($value, $key) {
            if (is_array($value)) {
                $array = collect($value)->map(function ($value) {
                    return $this->goDeep($value);
                });

                return $this->containsAssoc($array) ? $array : $array->collapse();
            }

            return $this->isValid($value, $key) ? $this->relatedData($value) : $value;
        });
    }

    /**
     * Find related data
     */
    private function relatedData($item)
    {
        if (Asset::find($item)) {
            $asset = Asset::find($item)->manipulate()->build();
            
            return URL::makeAbsolute($asset);
        }

        if (Content::exists($item)) {
            return Content::find($item)->data();
        }

        return $item;
    }

    /**
     * Check if value is not id
     */
    private function isValid($value, $key)
    {
        if ($key === 'id' || strlen($value) !== 36) {
            return false;
        }

        return true;
    }

    /**
     * Check if array is/contains an associative array
     */
    private function containsAssoc($array)
    {
        if ($this->isAssoc($array->toArray())) {
            return true;
        }

        return $array->filter(function ($value) {
            return $this->isAssoc(collect($value)->toArray());
        })->count();
    }

    /**
     * Check if array is an associative array
     */
    private function isAssoc($array)
    {
        if ($array === array()) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}