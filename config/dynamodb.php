<?php

return [
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'endpoint' => env('DYNAMODB_ENDPOINT'), // null in production, set for local dev
    'tables' => [
        'polls' => env('DYNAMODB_POLLS_TABLE', 'movie-mercredi-polls'),
        'votes' => env('DYNAMODB_VOTES_TABLE', 'movie-mercredi-votes'),
        'covers' => env('DYNAMODB_COVERS_TABLE', 'movie-mercredi-covers'),
    ],
];
