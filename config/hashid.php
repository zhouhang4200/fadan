<?php

return [

    'default' => 'id',

    'connections' => [

        'basic' => [
            'driver' => 'base64',
        ],

        'hashids' => [
            'driver' => 'hashids',
            'salt' => 'sweeddtDirl',
        ],

        'id' => [
            'driver' => 'hashids_integer',
            'salt' => 'Sdd8787413',
            'min_length' => 6,
            'alphabet' => '1234567890abcdef',
        ],

        'base62' => [
            'driver' => 'base62',
            'characters' => 'f9FkqDbzmn0QRru7PBVeGl5pU28LgIvYwSydK41sCO3htaicjZoWAJNxH6EMTX',
        ],

    ],
];
