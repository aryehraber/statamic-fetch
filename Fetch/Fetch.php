<?php

namespace Statamic\Addons\Fetch;

use Carbon\Carbon;
use Statamic\API\Str;
use Statamic\API\Page;
use Statamic\API\Term;
use Statamic\API\Asset;
use Statamic\API\Search;
use Statamic\API\Content;
use Statamic\API\GlobalSet;
use Statamic\API\Collection;
use Statamic\Extend\Extensible;
use Illuminate\Support\Collection as IlluminateCollection;

class Fetch
{
    use Extensible;

    public $auth;
    public $deep;
    public $debug;
    public $locale;

    private $page;
    private $limit;
    private $offset;
    private $filter;
    private $taxonomy;

    private $index;
    private $query;
    private $isSearch;

    private $hasNextPage;
    private $totalResults;

    public function __construct($params = null)
    {
        $params = collect($params);

        $this->auth = (new FetchAuth)->isAuth();
        $this->deep = $this->checkDeep($params);
        $this->debug = bool(request('debug'), $params->get('debug'));
        $this->locale = request('locale') ?: $params->get('locale') ?: default_locale();

        $this->page = (int) (request('page') ?: $params->get('page', 1));
        $this->limit = (int) (request('limit') ?: $params->get('limit'));
        $this->offset = (int) request('offset') ?: $params->get('offset');
        $this->filter = request('filter') ?: $params->get('filter');
        $this->taxonomy = request('taxonomy') ?: $params->get('taxonomy');

        $this->index = request('index') ?: $params->get('index');
        $this->query = request('query') ?: $params->get('query');
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
     * Fetch single global
     */
    public function global($handle = null)
    {
        $handle = $handle ?: request()->segment(4);

        if (! $global = GlobalSet::whereHandle($handle)) {
            return "Page [$handle] not found.";
        }

        return $this->handle($global);
    }

    /**
     * Fetch multiple globals
     */
    public function globals($globals = null)
    {
        $globals = $globals ?: request('globals');

        if (! is_null($globals) && ! is_array($globals)) {
            $globals = explode(',', $globals);
        }

        if ($globals) {
            $globals = collect($globals)->map(function ($handle) {
                return GlobalSet::whereHandle($handle);
            })->filter();
        } else {
            $globals = GlobalSet::all();
        }

        return $this->handle($globals);
    }

    /**
     * Fetch search results
     */
    public function search()
    {
        $this->isSearch = true;

        $data = $this->index
            ? Search::in($this->index)->search($this->query)
            : Search::get($this->query);

        return $this->handle($data);
    }

    /**
     * Handle data
     */
    private function handle($data)
    {
        $data = $this->taxonomizeData($data);
        $data = $this->filterData($data);

        $this->setTotalResults($data);

        $data = $this->offsetData($data);
        $data = $this->limitData($data);

        if ($this->deep) {
            $data = $this->processData($data);
        }

        $data = [
            'data' => $data,
            'page' => $this->page,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'has_next_page' => $this->hasNextPage,
            'total_results' => $this->totalResults,
        ];

        if ($this->debug) {
            dd($data);
        }

        return $data;
    }

    /**
     * Get processed data
     */
    private function processData($data)
    {
        if (! $data instanceof IlluminateCollection) {
            return $this->getLocalisedData($data);
        }

        return $data->map(function ($item) {
            $data = $this->getLocalisedData($item);

            if ($this->isSearch) {
                $data = collect($item)->merge($data->get('id'));
            }

            return $data;
        });
    }

    /**
     * Handle taxonomy filters
     */
    private function taxonomizeData($data)
    {
        if ($this->taxonomy) {
            $data = $data->filter(function ($entry) {
                $taxonomies = collect(explode('|', $this->taxonomy));

                return $taxonomies->first(function ($key, $value) use ($entry) {
                    list($taxonomy, $term) = explode('/', $value);

                    return collect($entry->get($taxonomy))
                        ->contains(function ($key, $value) use ($term) {
                            return $term === slugify($value);
                        });
                });
            });
        }

        return $data;
    }

    /**
     * Handle filtering data
     */
    private function filterData($data)
    {
        if (! in_array($this->filter, ['published', 'unpublished'])) {
            return $data;
        }

        $filter = 'filter'.Str::ucfirst($this->filter);

        if ($data instanceof IlluminateCollection) {
            $data = $data->filter(function ($entry) use ($filter) {
                return $this->$filter($entry);
            })->filter();
        } else {
            $data = $this->$filter($data);
        }

        return $data;
    }

    /**
     * Filter unpublished content
     */
    private function filterUnpublished($data)
    {
        return method_exists($data, 'published')
            ? ($data->published() ? null : $data)
            : $data;
    }

    /**
     * Filter published content
     */
    private function filterPublished($data)
    {
        return method_exists($data, 'published')
            ? ($data->published() ? $data : null)
            : $data;
    }

    /**
     * Handle offsetting data
     */
    private function offsetData($data)
    {
        if ($data instanceof IlluminateCollection && $this->offset) {
            $data = $data->slice($this->offset);
        }

        return $data;
    }

    /**
     * Handle limiting data
     */
    private function limitData($data)
    {
        if ($data instanceof IlluminateCollection && $this->limit) {
            $data = $data->forPage($this->page, $this->limit);

            $this->setHasNextPage();
        }

        return $data;
    }

    /**
     * Check if next page is available
     */
    private function setHasNextPage()
    {
        $count = $this->offset + ($this->page * $this->limit);

        $this->hasNextPage = ($this->totalResults - $count) > 0;
    }

    /**
     *
     */
    private function setTotalResults($data)
    {
        if ($data instanceof IlluminateCollection) {
            $this->totalResults = $data->count();
        }
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
                return collect($value)->map(function ($value) {
                   return $this->goDeep($value);
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

        if ($term = $this->findTerm($value)) {
            return $term;
        }

        if (Content::exists($value)) {
            return Content::find($value)->toArray();
        }

        if ($key === 'mount') {
            return $this->handle(Collection::whereHandle($value)->entries());
        }

        return $value;
    }

    /**
     * Find taxonomy term
     */
    private function findTerm($value)
    {
        if (strpos($value, '/') === false) {
            return null;
        }

        list($taxonomy, $slug) = explode('/', $value);

        return Term::whereSlug($slug, $taxonomy);
    }

    /**
     * Check if value could be relatable data
     */
    private function isRelatable($value, $key)
    {
        if ($key === 'id' && ! $this->isSearch) {
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

    private function checkDeep($params)
    {
        $param = request('deep', $params->get('deep'));

        return is_null($param) ? $this->getConfigBool('deep') : bool($param);
    }
}
