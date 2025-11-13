<?php

return [
    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),
    'skip_ip' => array_filter(explode(',', env('NOCAPTCHA_SKIP_IP', ''))),
    'options' => [
        'timeout' => 30,
    ],
];
