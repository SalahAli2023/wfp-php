<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

//Authentication middleware for route protection
class AuthMiddleware {
    private Session $session;
    private Response $response;

    public function __construct() {
        $this->session = new Session();
        $this->response = new Response();
    }

    //Check if user is authenticated
    public function handle(): bool {
        $user = $this->session->get('user');
        
        if (!$user) {
            $this->response->unauthorized('Authentication required');
            return false;
        }

        return true;
    }

    //Check if user has specific role
    public function requireRole(string $role): bool {
        $user = $this->session->get('user');
        
        if (!$user || $user['role'] !== $role) {
            $this->response->forbidden('Insufficient permissions');
            return false;
        }

        return true;
    }

    //Check if user has any of the required roles
    public function requireAnyRole(array $roles): bool {
        $user = $this->session->get('user');
        
        if (!$user || !in_array($user['role'], $roles)) {
            $this->response->forbidden('Insufficient permissions');
            return false;
        }

        return true;
    }
}