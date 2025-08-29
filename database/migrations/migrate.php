<?php
/**
 * Database migration script for Windows
 */

// Database configuration
$config = [
    'host' => 'localhost',
    'dbname' => 'wfp_db',
    'username' => 'root',
    'password' => '', // add your password here if needed
    'charset' => 'utf8mb4'
];

try {
    echo "Starting database migration...<br>";
    
    // Create database connection
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['dbname']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE {$config['dbname']}");
    
    echo "Database created/selected successfully.<br>";

    // Run migrations
    $migrationFiles = [
        '001_create_users_table.sql',
        '002_create_projects_table.sql'
    ];

    foreach ($migrationFiles as $file) {
        $filePath = __DIR__ . '/migrations/' . $file;
        if (file_exists($filePath)) {
            echo "Running migration: $file<br>";
            $sql = file_get_contents($filePath);
            $pdo->exec($sql);
            echo "✓ $file executed successfully.<br>";
        } else {
            echo "✗ Migration file not found: $file<br>";
        }
    }

    echo "<br>All migrations completed successfully!<br>";

} catch (PDOException $e) {
    echo " Migration failed: " . $e->getMessage() . "<br>";
    exit(1);
}