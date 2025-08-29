<?php
namespace App\Middleware;

use App\Core\Response;

class AdminMiddleware {
    private $response;

    public function __construct() {
        $this->response = new Response();
    }

    public function handle() {
        session_start();
        $user = $_SESSION['user'] ?? null;
        
        if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
            $this->response->error('Access denied. Admin privileges required.', 403);
            return false;
        }
        
        return true;
    }
}