<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Trusts the private network ranges so headers forwarded by the
     * edge-caddy reverse proxy (X-Forwarded-Proto etc.) are honored.
     * Without this, requests behind Caddy are seen as plain HTTP, which
     * breaks Livewire's signed upload URLs once URL::forceScheme('https')
     * kicks in for the production environment.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = [
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
    ];

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
