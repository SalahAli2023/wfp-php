<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// test database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=wfp_db;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection: OK<br>";
    
    // test users table
    $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
    $user = $stmt->fetch();
    echo "Users table: " . ($user ? "OK" : "EMPTY") . "<br>";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// test session
session_start();
echo "Session: OK<br>";

echo "Test completed!";