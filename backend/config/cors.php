<?php

return [

    /*
     * Paths that should have CORS headers applied.
     */
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    /*
     * Set FRONTEND_URL in .env on the server to your actual frontend origin.
     * Local dev default: http://localhost:5173
     */
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,

];
