<?php
namespace App\Core;

/* Session management with security features
 * Prevents fixation and hijacking attacks */

class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 86400, // 24 hours
                'cookie_secure' => false,
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax'
            ]);
        }
            if (empty($_SESSION['session_id'])) {
            $_SESSION['session_id'] = session_id();
        }
        // Regenerate ID periodically to prevent fixation
        if (!isset($_SESSION['last_regeneration'])) {
            $this->regenerate();
        } else {
            $interval = 1800; // 30 minutes
            if (time() - $_SESSION['last_regeneration'] > $interval) {
                $this->regenerate();
            }
        }
    }

    //Regenerate session ID for security
    private function regenerate(): void {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }

    //Set session value
    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    //Get session value
    public function get(string $key) {
        return $_SESSION[$key] ?? null;
    }

    //Remove session value
    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    //Destroy entire session
    public function destroy(): void {
        session_destroy();
        session_unset();
    }

    //Set flash message for one-time display
    public function setFlash(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    //Get and remove flash message
    public function getFlash(string $key): ?string {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
}