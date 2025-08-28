<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;

class AuthController {
    private $userModel;
    private $session;

    public function __construct() {
        $this->userModel = new User();
        $this->session = new Session();
    }

    public function register() {
        echo "1";
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validation
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        // Check if user exists
        if ($this->userModel->findByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(['error' => 'User already exists']);
            return;
        }

        // Create user
        $userId = $this->userModel->create($data);
        
        // Create session
        $user = $this->userModel->findById($userId);
        $this->session->set('user', $user);

        http_response_code(201);
        echo json_encode(['message' => 'User created successfully', 'user' => $user]);
    }

    public function login() {
        echo "1";
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        $user = $this->userModel->findByEmail($data['email']);
        
        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        // Remove password from response
        unset($user['password']);
        $this->session->set('user', $user);

        echo json_encode(['message' => 'Login successful', 'user' => $user]);
    }

    public function logout() {
        $this->session->destroy();
        echo json_encode(['message' => 'Logout successful']);
    }

    public function me() {
        $user = $this->session->get('user');
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }
        echo json_encode(['user' => $user]);
    }

    public function updateProfile() {
        $user = $this->session->get('user');
        if (!$user) {
            $this->response->unauthorized();
            return;
        }

        $data = $this->request->getJson();
        
        // Validation and update logic here
        // ...
    }

    public function changePassword() {
        $user = $this->session->get('user');
        if (!$user) {
            $this->response->unauthorized();
            return;
        }

        $data = $this->request->getJson();
        
        // Password change logic here
        // ...
    }
}