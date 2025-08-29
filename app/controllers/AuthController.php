<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;
use App\Core\Response;

class AuthController {
    private $userModel;
    private $session;
    private $response;

    public function __construct() {
        $this->userModel = new User();
        $this->session = new Session();
        $this->response = new Response();
    }

    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validation
        if (!isset($data['email']) || !isset($data['password'])) {
            $this->response->error('Email and password are required');
            return;
        }

        // Check if user exists
        if ($this->userModel->findByEmail($data['email'])) {
            $this->response->error('User already exists',409);
            return;
        }

        // Create user
        $userId = $this->userModel->create($data);
        
        // Create session
        $user = $this->userModel->findById($userId);
        $this->session->set('user', $user);
        $this->response->error(['message' => 'User created successfully', 'user' => $user],201);
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            $this->response->error('Email and password are required', 400);
            return;
        }

        // Find user
        $user = $this->userModel->findByEmail($data['email']);
        
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $this->response->error('Invalid credentials', 401);
            return;
        }

        // Remove password from response
        unset($user['password']);

        // Create session
        $this->session->set('user', $user);

        $this->response->success(['user' => $user ,'message' => 'Login successful']);
    }

    public function logout() {
        $this->session->destroy();
        echo json_encode(['message' => 'Logout successful']);
        // $this->response->success(['message' => 'Logout successful']);
    }

    public function me() {
        $user = $this->session->get('user');
        if (!$user) {
            $this->response->error('Not authenticated', 401);
            return;
        }
        echo json_encode(['user' => $user]);
    }

    public function getProfile() {
        $user = $this->session->get('user');
        
        if (!$user) {
            $this->response->error('Not authenticated', 401);
            return;
        }
        
        // Get fresh user data from database
        $freshUser = $this->userModel->findById($user['id']);
        if (!$freshUser) {
            $this->response->error('User not found', 404);
            return;
        }
        
        unset($freshUser['password']);
        
        $this->response->success(['user' => $freshUser]);
    }

    public function updateProfile() {
        $user = $this->session->get('user');
        
        if (!$user) {
            $this->response->unauthorized('Not authenticated');
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validation and update logic here
        if (empty($data['name']) || empty($data['email'])) {
            $this->response->error('Name and email are required', 400);
            return;
        }

        // Check that the email has not been used before the last user
        $existingUser = $this->userModel->findByEmail($data['email']);
        if ($existingUser && $existingUser['id'] != $user['id']) {
            $this->response->error('Email already taken', 409);
            return;
        }

        // Update user in database
        $success = $this->userModel->update($user['id'], [
            'name' => $data['name'],
            'email' => $data['email']
        ]);

        if ($success) {
            // Update session with new data
            $updatedUser = $this->userModel->findById($user['id']);
            unset($updatedUser['password']);
            $this->session->set('user', $updatedUser);
            
            $this->response->success(['message' => 'Profile updated successfully', 'user' => $updatedUser]);
        } else {
            $this->response->error('Failed to update profile',500);
        }
    }

    public function changePassword() {
        $user = $this->session->get('user');

        if (!$user) {
            $this->response->unauthorized();
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validation
        if (empty($data['current_password']) || empty($data['new_password'])) {
            $this->response->error('Current and new password are required');
            return;
        }

        if ($data['new_password'] !== $data['confirm_password']) {
            $this->response->error(['New passwords do not match']);
            return;
        }
        
        if (strlen($data['new_password']) < 6) {
            $this->response->error('New password must be at least 6 characters', 400);
            return;
        }

        // Verify current password
        $userData = $this->userModel->findById($user['id']);
        if (!password_verify($data['current_password'], $userData['password'])) {
            $this->response->error('Current password is incorrect');
            return;
        }

        // Update password
        $success = $this->userModel->updatePassword($user['id'], $data['new_password']);

        if ($success) {
            $this->response->success(['message' => 'Password updated successfully']);
        } else {
            $this->response->error('Failed to update password',500);
        }
    }
}