<?php

return [
    'cache' => [

        'prefix' => env('LPQ_CACHE_PREFIX', 'persisted_query'),

        'ttl' => env('LPQ_CACHE_TTL', 0),

        'max-age' => env('LPQ_CACHE_MAX_AGE', 86400),

    ],

    'excluded_operations' => [
        //
    ],

];
