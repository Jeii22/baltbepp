<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. We will use Argon2id for stronger
    | security over bcrypt. Ensure your PHP build supports Argon2.
    |
    */

    'driver' => 'argon2id',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    */

    'argon' => [
        // Memory cost in kibibytes (64MB default)
        'memory' => env('HASH_ARGON_MEMORY', 65536),

        // Time cost (iterations)
        'time' => env('HASH_ARGON_TIME', 4),

        // Number of parallel threads
        'threads' => env('HASH_ARGON_THREADS', 1),
    ],

];