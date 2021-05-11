<?php

namespace BBSLab\LighthousePersistedQueries;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PersistsQuery
{
    protected bool $shouldAddCacheHeaders = false;

    public function handle(Request $request, Closure $next)
    {
        $extensions = $this->extensions($request);

        if (! $hash = data_get($extensions, 'persistedQuery.sha256Hash')) {
            return $next($request);
        }

        $key = static::cacheKey($hash);

        if ($request->isMethod(Request::METHOD_GET)) {
            if (Cache::missing($key)) {
                return $this->sendErrorResponse($extensions);
            }

            $request = $this->replaceRequest($request, Cache::get($key));
            $this->shouldAddCacheHeaders = true;
        } elseif ($request->isMethod(Request::METHOD_POST)) {
            $this->persistQuery($key, $request->input('query'));
        }

        $this->verifyCacheExclusions($request);

        $response = $next($request);

        if ($this->shouldAddCacheHeaders) {
            $ttl = config('lighthouse-persisted-queries.cache.max-age', 86400);
            $response->header('Cache-Control', "public, max-age={$ttl}");
        }

        return $response;
    }

    protected function extensions(Request $request): array
    {
        $extensions = $request->input('extensions');

        if (is_array($extensions)) {
            return $extensions;
        }

        if (! is_string($extensions) || empty($extensions)) {
            return [];
        }

        return json_decode($extensions, true);
    }

    public static function cacheKey(string $hash): string
    {
        $prefix = config('lighthouse-persisted-queries.cache.prefix', '');

        if (! is_string($prefix) || empty($prefix)) {
            return $hash;
        }

        $prefix = trim($prefix, '_');

        return "{$prefix}_{$hash}";
    }

    protected function sendErrorResponse(array $extensions): JsonResponse
    {
        return response()
            ->json([
                'errors' => [
                    [
                        'message' => 'PersistedQueryNotFound',
                        'extensions' => $extensions,
                    ],
                ],
            ])
            ->header('Cache-Control', 'private, no-store');
    }

    protected function replaceRequest(Request $request, $query): Request
    {
        $request->setMethod(Request::METHOD_POST);
        $request->merge(['query' => $query]);

        $request = $this->createRequestFrom($request, json_encode($request->all()));

        app()->instance(Request::class, $request);

        return $request;
    }

    protected function createRequestFrom(Request $from, string $content): Request
    {
        $request = new Request;

        $files = $from->files->all();

        $files = is_array($files) ? array_filter($files) : $files;

        $request->initialize(
            $from->query->all(),
            $from->request->all(),
            $from->attributes->all(),
            $from->cookies->all(),
            $files,
            $from->server->all(),
            $content
        );

        $request->headers->replace($from->headers->all());

        $request->setJson($from->json());

        if ($session = $from->getSession()) {
            $request->setLaravelSession($session);
        }

        $request->setUserResolver($from->getUserResolver());

        $request->setRouteResolver($from->getRouteResolver());

        return $request;
    }

    protected function persistQuery(string $key, $query): void
    {
        $ttl = config('lighthouse-persisted-queries.cache.ttl', 0);

        $callback = fn () => $query;

        $ttl > 0 ? Cache::remember($key, $ttl, $callback) : Cache::rememberForever($key, $callback);
    }

    protected function verifyCacheExclusions(Request $request): void
    {
        $operationName = $request->input('operationName');

        if (! is_string($operationName) || empty($operationName)) {
            return;
        }

        if (! in_array($operationName, config('lighthouse-persisted-queries.excluded_operations', []))) {
            return;
        }

        $this->shouldAddCacheHeaders = false;
    }
}
