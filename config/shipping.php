<?php

return [
    'shipping_origin_code' => env('SHIPPING_ORIGIN_CODE', '32.73.14.1002'),
    'api_kurir' => [
        'username' => env('API_KURIR_USERNAME'),
        'password' => env('API_KURIR_PASSWORD'),
    ],
    // config/shipping.php
    // 'api_kurir' => [
    //     'base_url' => env('API_KURIR_BASE_URL', 'https://sandbox.apikurir.id/shipments/v1/open-api'),
    //     'username' => env('API_KURIR_USERNAME'),
    //     'password' => env('API_KURIR_PASSWORD'),
    // ],
];
