<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    $kernel->call('migrate', ['--force' => true]);
    echo 'Migrations completed successfully!';
    echo '<br>Output: ' . $kernel->output();
} catch (Exception $e) {
    echo 'Migration failed: ' . $e->getMessage();
}