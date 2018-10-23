<?php

namespace Statamic\Addons\Fetch;

use Statamic\Extend\Extensible;

class FetchAuth
{
    use Extensible;

    public function isAuth()
    {
        if ($this->getConfig('enable_api_key', false) && ! $this->checkApiKey()) {
            return false;
        }

        if (! empty($this->getConfig('ip_whitelist', [])) && ! $this->checkServerAddr()) {
            return false;
        }

        if (! empty($this->getConfig('domain_whitelist', [])) && ! $this->checkRemoteDomain()) {
            return false;
        }

        return true;
    }

    private function checkServerAddr()
    {
        $ip = getenv('HTTP_CLIENT_IP') ?: getenv('HTTP_X_FORWARDED_FOR') ?: getenv('REMOTE_ADDR');

        if (in_array($ip, $this->getConfig('ip_whitelist', []))) {
            return true;
        }

        return false;
    }

    private function checkApiKey()
    {
        if (request('api_key') == $this->getConfig('api_key')) {
            return true;
        }

        return false;
    }

    private function checkRemoteDomain()
    {
        if (! getenv('HTTP_ORIGIN')) {
            return true;
        }

        if (in_array(getenv('HTTP_ORIGIN'), $this->getConfig('domain_whitelist', []))) {
            return true;
        }

        return false;
    }
}
