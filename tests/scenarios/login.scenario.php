<?php

return [
    [
        'a user exists',
        'they enter valid credentials',
        'they are authenticated',
        'they are redirected to the home page',
    ],
    [
        'a user exists',
        'they enter invalid credentials',
        'they are not authenticated',
    ],
];
