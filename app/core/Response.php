<?php
namespace App\Core;

// Standardized JSON response handler
class Response {
    //Send JSON response with status code
    public function json(
        array $data, 
        int $statusCode = 200, 
        array $headers = []
    ): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        // Add custom headers
        foreach ($headers as $key => $value) {
            header("$key: $value");
        }
        
        echo json_encode($data);
        exit;
    }

    //success response
    public function success(
        array $data = [], 
        string $message = 'Success', 
        int $statusCode = 200
    ): void {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    //Error response
    public function error(
        string $message = 'Error', 
        int $statusCode = 400, 
        array $errors = []
    ): void {
        $this->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    //Not found response
    public function notFound(string $message = 'Resource not found'): void {
        $this->error($message, 404);
    }

    //Unauthorized response
    public function unauthorized(string $message = 'Unauthorized'): void {
        $this->error($message, 401);
    }

    //Forbidden response
    public function forbidden(string $message = 'Forbidden'): void {
        $this->error($message, 403);
    }
}