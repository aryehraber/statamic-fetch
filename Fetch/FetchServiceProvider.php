<?php

namespace Statamic\Addons\Fetch;

use Statamic\API\Config;
use Statamic\Extend\ServiceProvider;

class FetchServiceProvider extends ServiceProvider
{
    public $defer = true;

    public function boot()
    {
        $excludes = Config::get('system.csrf_exclude', []);
        $actionUrl = $this->actionUrl('*');

        if (! in_array($actionUrl, $excludes)) {
            $excludes[] = $actionUrl;
            Config::set('system.csrf_exclude', $excludes);
        }
    }
}