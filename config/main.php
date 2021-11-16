<?php

return [
    'connections' => [
        // 'bafrica' => env('DB_CONNECTION', 'mysql'), // no need to define it, because it is a default connection
        'maxinemo' => env('DB_MAXINEMO_CONNECTION', 'maxinemo')
    ],
    'databases' => [
        'bafrica' => env('DB_DATABASE', null),
        'maxinemo' => env('DB_MAXINEMO_DATABASE', null)
    ]
];