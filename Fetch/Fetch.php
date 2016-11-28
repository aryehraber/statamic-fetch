<?php

namespace Statamic\Addons\Fetch;

use Statamic\API\URL;
use Statamic\API\Asset;
use Statamic\API\Content;
use Statamic\Extend\Extensible;
use Statamic\Assets\Asset as Image;

class Fetch
{
    use Extensible;

    public $auth;

    public function __construct()
    {
        $this->auth = (new FetchAuth)->isAuth();
    }

    public function handle($collection, $deep = null, $debug = null)
    {
        $entries = $collection->entries();

        if ($deep || $this->getConfigBool('deep')) {
            $entries = $entries->map(function ($entry) {
                return $this->goDeep($entry);
            });
        }

        if ($debug) {
            dd($entries);
        }

        return $entries;
    }

    public function goDeep($item)
    {
        return collect($item)->map(function ($value, $key) {
            if (is_array($value)) {
                $array = collect($value)->map(function ($value) {
                    return $this->goDeep($value);
                });

                return $this->isAssoc($array->toArray()) ? $array : $array->collapse();
            }

            return $this->isValid($value, $key) ? $this->relatedData($value) : $value;
        });
    }

    public function relatedData($item)
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

    public function isValid($value, $key)
    {
        if ($key === 'id' || strlen($value) !== 36) {
            return false;
        }

        return true;
    }

    public function isAssoc($array)
    {
        if (array() === $array) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}