<?php

namespace Statamic\Addons\Fetch;

use Carbon\Carbon;
use Statamic\API\Str;
use Statamic\API\Page;
use Statamic\API\Term;
use Statamic\API\Asset;
use Statamic\API\Content;
use Statamic\API\Taxonomy;
use Statamic\API\Collection;
use Statamic\Extend\Extensible;
use Statamic\Data\Pages\Page as PageData;

class Fetch
{
    use Extensible;

    public $auth;
    public $deep;
    public $debug;
    public $locale;

    public function __construct($params = null)
    {
        $params = collect($params);

        $this->auth = (new FetchAuth)->isAuth();
        $this->deep = bool(request('deep')) || $this->getConfigBool('deep') || $params->get('deep');
        $this->debug = bool(request('debug')) || $params->get('debug');
        $this->locale = request('locale') ?: $params->get('locale') ?: default_locale();
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
                $data = $this->getLocalisedData($data);
            } else {
                $data = $data->map(function ($item) {
                    return $this->getLocalisedData($item);
                });
            }
        }

        if ($this->debug) {
            dd($data);
        }

        return $data;
    }

    /**
     * Get localised data
     */
    private function getLocalisedData($rawData)
    {
        $data = $this->goDeep($rawData);

        if ($this->locale !== default_locale()) {
            $localisedData = $this->goDeep($rawData->dataForLocale($this->locale));

            $data = $data->merge($localisedData);
        }

        return $data;
    }

    /**
     * Fetch item data recursively
     */
    private function goDeep($item)
    {
        $item = collect($item)->map(function ($value, $key) {
            if (is_array($value)) {
                return collect($value)->map(function ($value) use ($key) {
                    if (Taxonomy::handleExists($key)) {
                        return $this->relatedData($value, $key);
                    }

                    return is_string($value) ? $this->goDeep($value) : $value;
                });
            }

            return $this->isRelatable($value, $key) ? $this->relatedData($value, $key) : $value;
        });

        return $item->count() === 1 ? $item->first() : $item;
    }

    /**
     * Find related data
     */
    private function relatedData($value, $key)
    {
        if ($asset = Asset::find($value)) {
            return $asset->absoluteUrl();
        }

        if ($term = Term::whereSlug($value, $key)) {
            return $term;
        }

        if (Content::exists($value)) {
            return Content::find($value)->data();
        }

        return $value;
    }

    /**
     * Check if value could be relatable data
     */
    private function isRelatable($value, $key)
    {
        if ($key === 'id') {
            return false;
        }

        if (is_bool($value)) {
            return false;
        }

        if ($value instanceof Carbon) {
            return false;
        }

        return true;
    }
}
