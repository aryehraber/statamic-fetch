<?php

namespace Statamic\Addons\Fetch;

use Statamic\API\Config;
use Statamic\Extend\ServiceProvider;

class FetchServiceProvider extends ServiceProvider
{
    public $defer = true;

    public function register()
    {
        $this->app->singleton(FetchAuth::class, function () {
            return new FetchAuth();
        });

        $this->app->singleton(Fetch::class, function ($app) {
            return new Fetch($app[FetchAuth::class]);
        });
    }

    public function boot()
    {
        $excludes = Config::get('system.csrf_exclude', []);
        $actionUrl = $this->actionUrl('*');

        if (! in_array($actionUrl, $excludes)) {
            $excludes[] = $actionUrl;
            Config::set('system.csrf_exclude', $excludes);
            Config::save();
        }
    }

    public function provides()
    {
        return [
            FetchAuth::class,
            Fetch::class,
        ];
    }
}
