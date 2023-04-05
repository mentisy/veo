<?php

return [
    'App' => [
        'namespace' => 'Avolle\\Veo\\',
    ],
    'session' => null, // Your Veo session id (can be found in your web cookie)
    'timezone' => '+01:00',
    // Cameras to scan for
    'cameras' => [
        'FF:FF:FF:FF:FF:FE', // Camera #1
        'FF:FF:FF:FF:FF:FF', // Camera #2
    ],
    // Teams to scan for
    'teams' => [
        'Team #1',
        'Team #2',
    ],
    // Details of all known teams with Veo
    'teamsFull' => [
        [
            'active' => true,
            'name' => 'Some Team',
            'camera' => 'FF:FF:FF:FF:FF:FF', // Camera's MAC Address / Serial Number
            'club_id' => '', // Club's hex id
            'club_slug' => '', // Club's slug
            'team_id' => '', // Team's hex id
            'team_slug' => '', // Team's slug
        ],
    ],
];
