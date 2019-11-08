<?php

namespace Statamic\Addons\Fetch;

use Illuminate\Http\Response;
use Statamic\Extend\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class FetchController extends Controller
{
    protected $fetch;
    protected $cacheEnabled;
    protected $cacheCreatedAt;
    protected $cacheTtl;

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
        return $this->cache(function () {
            return $this->response($this->fetch->collection());
        });
    }

    public function postCollection()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->collection());
        });
    }

    public function getEntry()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->entry());
        });
    }

    public function postEntry()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->entry());
        });
    }

    public function getPage()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->page());
        });
    }

    public function postPage()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->page());
        });
    }

    public function getPages()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->pages());
        });
    }

    public function postPages()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->pages());
        });
    }

    public function getGlobal()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->global());
        });
    }

    public function postGlobal()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->global());
        });
    }

    public function getGlobals()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->globals());
        });
    }

    public function postGlobals()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->globals());
        });
    }

    public function getSearch()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->search());
        });
    }

    public function postSearch()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->search());
        });
    }

    public function getTaxonomies()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->taxonomies());
        });
    }

    public function postTaxonomies()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->taxonomies());
        });
    }

    public function getTaxonomy()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->taxonomy());
        });
    }

    public function postTaxonomy()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->taxonomy());
        });
    }

    public function getAssets()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->assets());
        });
    }

    public function postAssets()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->assets());
        });
    }

    public function getAsset()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->asset());
        });
    }

    public function postAsset()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->asset());
        });
    }

    public function getUser()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->user());
        });
    }

    public function postUser()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->user());
        });
    }

    public function getUsers()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->users());
        });
    }

    public function postUsers()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->users());
        });
    }

    public function getFormset()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->formset());
        });
    }

    public function postFormset()
    {
        return $this->cache(function () {
            return $this->response($this->fetch->formset());
        });
    }

    public function getClearCache()
    {
        $command = 'clear:cache';

        try {
            Artisan::call($command);

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Problem running command: ' . $command);

            return ['errors' => 'Problem running command: ' . $command];
        }
    }

    protected function cache($response)
    {
        $this->cacheEnabled = $this->getConfig('enable_caching') !== null && $this->getConfig('enable_caching') ? 1 : 0;
        $this->cacheCreatedAt = time();
        $this->cacheTtl = $this->getConfig('cache_ttl') !== null ? (int)$this->getConfig('cache_ttl') : 86400;

        if ($this->cacheEnabled) {
            $hash = md5(request()->decodedPath() . implode('', request()->all()));

            return Cache::remember($hash, $this->cacheTtl, function () use ($response) {
                return call_user_func($response);
            });
        }

        return call_user_func($response);
    }

    protected function response($data)
    {
        $response = $data instanceof Response ? $data : $this->createResponse($data);

        $this->emitEvent('response.created', $response);

        return $response;
    }

    protected function createResponse($data)
    {
        $response = response()->json(
            $data instanceof Collection ? $data->toArray() : []
        );

        $response->header('Statamic-Fetch-Cache-Enabled', $this->cacheEnabled);

        if ($this->cacheEnabled) {
            $response->header('Statamic-Fetch-Cache-Created-At', $this->cacheCreatedAt);
            $response->header('Statamic-Fetch-Cache-TTL', $this->cacheTtl);
        }

        return $response;
    }
}
