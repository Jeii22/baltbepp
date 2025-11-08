<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Boots the Laravel application for tests (custom implementation because the framework trait was not found).
     */
    public function createApplication()
    {
        // Require the bootstrap file to get the application instance.
        $app = require __DIR__.'/../bootstrap/app.php';

        // Bootstrap the console kernel.
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
