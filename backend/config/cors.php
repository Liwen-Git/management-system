<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    // 允许响应头中携带 Access-Control-Expose-Headers : 'Authorization'
    'exposed_headers' => ['Authorization'],

    'max_age' => 0,

    /*
     * 与axios对应的配置
     * withCredentials: true, // send cookies when cross-domain requests
     */
    'supports_credentials' => false,

];
