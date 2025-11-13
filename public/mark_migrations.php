<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

try {
    // Get all migration files
    $migrationPath = __DIR__.'/../database/migrations';
    $files = scandir($migrationPath);
    
    $db = $app->make('db');
    
    // Ensure migrations table exists
    if (!$db->getSchemaBuilder()->hasTable('migrations')) {
        $db->getSchemaBuilder()->create('migrations', function($table) {
            $table->id();
            $table->string('migration');
            $table->integer('batch');
        });
    }
    
    // Get already run migrations
    $ranMigrations = $db->table('migrations')->pluck('migration')->toArray();
    
    echo "<h2>Migration Status:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Migration</th><th>Status</th><th>Action</th></tr>";
    
    $nextBatch = $db->table('migrations')->max('batch') + 1;
    $marked = 0;
    
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
        
        $migrationName = pathinfo($file, PATHINFO_FILENAME);
        
        // Skip the meta migration - we want this to actually run
        if (strpos($migrationName, 'add_meta_to_bookings') !== false) {
            echo "<tr><td>{$migrationName}</td><td style='color: blue;'>PENDING</td><td>Will run normally</td></tr>";
            continue;
        }
        
        $isRan = in_array($migrationName, $ranMigrations);
        
        if (!$isRan) {
            // Mark as run without executing
            $db->table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $nextBatch
            ]);
            $marked++;
            echo "<tr><td>{$migrationName}</td><td style='color: green;'>MARKED AS RAN</td><td>Added to migrations table</td></tr>";
        } else {
            echo "<tr><td>{$migrationName}</td><td style='color: gray;'>ALREADY RAN</td><td>-</td></tr>";
        }
    }
    
    echo "</table>";
    echo "<br><p><strong>Marked {$marked} migrations as complete.</strong></p>";
    echo "<p>Now visit <a href='migrate.php'>migrate.php</a> to run only the meta column migration.</p>";
    
} catch (Exception $e) {
    echo '<h2>Error:</h2>';
    echo '<p style="color: red;">' . $e->getMessage() . '</p>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
