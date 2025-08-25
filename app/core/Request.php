<?php
namespace App\Core;

/**
 * HTTP request handler
 * Parses input data from various sources
 */
class Request {
    //Get JSON input from request body
    public function getJson(): array {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    //Get query parameters
    public function getQuery(): array {
        return $_GET;
    }

    //Get form data
    public function getForm(): array {
        return $_POST;
    }

    //Get uploaded files
    public function getFiles(): array {
        return $_FILES;
    }

    //Get specific header value
    public function getHeader(string $name): ?string {
        $headers = getallheaders();
        return $headers[$name] ?? null;
    }

    //Get authorization token from header
    public function getAuthToken(): ?string {
        $authHeader = $this->getHeader('Authorization');
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }

    //Get client IP address
    public function getClientIp(): string {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    //Get request method
    public function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }
}