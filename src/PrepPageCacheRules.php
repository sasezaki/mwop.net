<?php
namespace Mwop;

use Phly\Http\Uri;

class PrepPageCacheRules
{
    private $xml = 'zpk/scripts/pagecache_rules.xml';

    public function __invoke($route, $console)
    {
        $appId = $route->getMatchedParam('appId');
        $site  = $route->getMatchedParam('site');

        $uri = new Uri(trim($site, '/') . '/');
        $port = $uri->port;
        if (! $port) {
            $port = ($uri->scheme == 'https') ? 443 : 80;
        }

        $rules = file_get_contents($this->xml);
        $rules = str_replace('%SCHEME%', $uri->scheme, $rules);
        $rules = str_replace('%HOST%',   $uri->host,   $rules);
        $rules = str_replace('%PORT%',   $port,        $rules);
        $rules = str_replace('%APPID%',  $appId,       $rules);

        file_put_contents($this->xml, $rules);
        return 0;
    }
}
