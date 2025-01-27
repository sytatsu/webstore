<?php

return [
    'sytatsy' => [
        'instagram' => [
            'enabled' => env(key: 'INSTAGRAM_ENABLED', default: false),
            'href' => env(key: 'INSTAGRAM_HREF', default: 'https://www.instagram.com/sytatsu_official/'),
        ],

        'x' => [
            'enabled' => env(key: 'X_ENABLED', default: false),
            'href' => env(key: 'X_HREF', default: 'https://www.twitter.com/sytatsu/'),
        ],

        'facebook' => [
            'enabled' => env(key: 'FACEBOOK_ENABLED', default: false),
            'href' => env(key: 'FACEBOOK_HREF', default: 'https://www.facebook.com/sytatsu.official'),
        ],

        'cardmarket' => [
            'enabled' => env(key: 'CARDMARKET_ENABLED', default: false),
            'href' => env(key: 'CARDMARKET_HREF', default: 'https://www.cardmarket.com/en/Pokemon/Users/StPronk')
        ]
    ]
];
