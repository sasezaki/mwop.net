<?php
namespace Mwop;

class NotAllowed
{
    public function __invoke($err, $req, $res, $next)
    {
        if ($res->getStatusCode() !== 405) {
            return $next($err);
        }

        if (is_array($err) || is_string($err)) {
            $res->setHeader('Allow', $err);
        }

        $res->end();
    }
}
