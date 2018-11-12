<?php

namespace Statamic\Addons\Fetch;

use Carbon\Carbon;
use Statamic\API\Str;
use Statamic\API\Page;
use Statamic\API\Term;
use Statamic\API\Asset;
use Statamic\API\Content;
use Statamic\API\Taxonomy;
use Statamic\API\GlobalSet;
use Statamic\API\Collection;
use Statamic\Extend\Extensible;
use Statamic\Data\Entries\Entry;
use Statamic\Data\Pages\Page as PageData;
use Statamic\Data\Taxonomies\Term as TermData;
use Statamic\Data\Globals\GlobalSet as GlobalData;
use Statamic\Data\Taxonomies\Taxonomy as TaxonomyData;

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
     * Fetch taxonomy
     */
    public function taxonomy($handle = null)
    {
        $handle = $handle ?: request()->segment(4);

        if (!$taxonomy = Taxonomy::whereHandle($handle)) {
            return "Taxonomy [$handle] not found.";
        }

        return $this->handle($taxonomy);
    }

    /**
     * Fetch taxonomies
     */
    public function taxonomies($taxonomies = null)
    {
        $taxonomies = $taxonomies ?: request('taxonomies');

        if (!is_null($taxonomies) && !is_array($taxonomies)) {
            $taxonomies = explode(',', $taxonomies);
        }

        if ($taxonomies) {
            $taxonomies = collect($taxonomies)->map(function ($handle) {
                return Taxonomy::whereHandle($handle);
            })->filter();
        } else {
            $taxonomies = Taxonomy::all();
        }

        return $this->handle($taxonomies);
    }

    /**
     * Fetch term
     */
    public function term($slug = null)
    {
        if ($slug) {
            list($taxonomy, $slug) = explode('/', $slug, 2);
        } else {
            $taxonomy = request()->segment(4);
            $slug = request()->segment(5);
        }

        if (!$term = Term::whereSlug($slug, $taxonomy)) {
            return "Term [$taxonomy/$slug] not found.";
        }

        return $this->handle($term);
    }

    /**
     * Fetch terms
     */
    public function terms($terms = null)
    {
        $terms = $terms ?: request('terms');

        if (!is_null($terms) && !is_array($terms)) {
            $terms = explode(',', $terms);
        }

        if ($terms) {
            $terms = collect($terms)->map(function ($slug) {
                list($taxonomy, $slug) = explode('/', $slug, 2);

                return Term::whereSlug($slug, $taxonomy);
            })->filter();
        } else {
            $terms = Term::all();
        }

        return $this->handle($terms);
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
            if (strpos('/' . request()->path(), $this->actionUrl('page')) !== false) {
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
     * Handle data
     */
    private function handle($data)
    {
        if ($this->deep) {
            if ($data instanceof PageData || $data instanceof GlobalData || $data instanceof TaxonomyData || $data instanceof TermData) {
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
        if ($item instanceof Taxonomy) {
            $item = [$item->data()];
        }

        $item = collect($item)->map(function ($value, $key) {
            if (is_array($value)) {
                return collect($value)->map(function ($value, $key) {
                    return $this->goDeep([$key => $value]);
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
            return Content::find($value)->data();
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
