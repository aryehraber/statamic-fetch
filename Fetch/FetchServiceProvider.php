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
        
        $excludes[] = $this->actionUrl('*');
        Config::set('system.csrf_exclude', $excludes);
    }
}