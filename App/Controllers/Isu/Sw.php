<?php

namespace App\Controllers\Isu;

/**
 * Sw — service worker for the ISU console PWA.
 *
 * Served at /isu/sw (public, not gated) so the worker's scope is /isu/ and
 * it controls every console page. Kept intentionally minimal: it enables
 * installability and passes requests through network-first — it does NOT
 * cache HTML/authenticated pages, since a management console needs live data.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class Sw extends \Core\Controller
{
    public function indexAction()
    {
        header('Content-Type: application/javascript; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Service-Worker-Allowed: /isu/');

        echo <<<'JS'
// ISU Console service worker
self.addEventListener('install', function (e) { self.skipWaiting(); });
self.addEventListener('activate', function (e) { e.waitUntil(self.clients.claim()); });
self.addEventListener('fetch', function (e) {
    // Network-first; fall back to any cached copy only if offline.
    e.respondWith(
        fetch(e.request).catch(function () { return caches.match(e.request); })
    );
});
JS;
        exit;
    }
}
