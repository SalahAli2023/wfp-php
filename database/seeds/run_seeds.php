<?php
/**
 * Database seeder execution script
 * Run with: php database/seeds/run_seeds.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/core/Database.php';

// Include seeders
require_once 'UsersSeeder.php';
require_once 'ProjectsSeeder.php';
require_once 'DatabaseSeeder.php';

use Database\Seeds\DatabaseSeeder;

echo "========================================<br>";
echo "WFP Database Seeder<br>";
echo "========================================<br>";

try {
    $seeder = new DatabaseSeeder();
    
    // Check for command line arguments
    if (isset($argv[1]) && $argv[1] === '--no-truncate') {
        $seeder->runWithoutTruncate();
    } else {
        $seeder->run();
    }

    echo "========================================<br>";
    echo "Seeding completed successfully!<br>";
    echo "========================================<br>";

} catch (Exception $e) {
    echo "========================================<br>";
    echo "Seeding failed!<br>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "========================================<br>";
    exit(1);
}