<?php

return [
    'chat' => [
        'base_url' => env('TENCENT_CHAT_BASE_URL', 'https://adminapikr.im.qcloud.com'),
        'sdkAppId' => env('TENCENT_SDK_APP_ID', 20013744),
        'identifier' => env('TENCENT_IDENTIFIER', 'administrator'),
        'secretKey' => env('TENCENT_SECRET_KEY', '135918f4f62a49fccc110cd838ff059128f28ed0768a17c98f639dfc4887c999'),
        'manager_user' => env('TENCENT_MANAGER_USER', 'deet_manager'),
    ],
];
