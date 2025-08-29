<?php
namespace App\Core;

class Auth {
    public static function check() {
        session_start();
        return $_SESSION['user'] ?? null;
    }

    public static function isAdmin() {
        $user = self::check();
        return $user && isset($user['role']) && $user['role'] === 'admin';
    }

    public static function user() {
        return self::check();
    }
}