<?php
namespace App\Controllers;

use App\Core\Response;
use App\Models\Donation;
use App\Core\Auth;

class DonationController {
    private $response;
    private $donationModel;

    public function __construct() {
        $this->response = new Response();
        $this->donationModel = new Donation();
    }

    //Get all donations
    public function index() {
        if (!Auth::isAdmin()) {
            $this->response->error('Unauthorized access', 403);
            return;
        }

        $filters = [
            'project_id' => $_GET['project_id'] ?? null,
            'user_id' => $_GET['user_id'] ?? null,
            'status' => $_GET['status'] ?? null
        ];

        $donations = $this->donationModel->getAll($filters);
        $this->response->success(['donations' => $donations]);
    }

    //Get donation by ID
    public function show($id) {
        if (!Auth::isAdmin()) {
            $this->response->error('Unauthorized access', 403);
            return;
        }

        $donation = $this->donationModel->getById($id);
        if (!$donation) {
            $this->response->error('Donation not found', 404);
            return;
        }

        $this->response->success(['donation' => $donation]);
    }

    //Create new donation
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validation
        if (empty($data['project_id']) || empty($data['amount']) || empty($data['donor_name']) || empty($data['donor_email'])) {
            $this->response->error('Project ID, amount, donor name and email are required', 400);
            return;
        }

        // For authenticated users, use their user_id
        session_start();
        if (isset($_SESSION['user'])) {
            $data['user_id'] = $_SESSION['user']['id'];
        } else {
            // For guest donations, create a guest user record or use 0
            $data['user_id'] = 0;
        }

        $success = $this->donationModel->create($data);
        if ($success) {
            $this->response->success(['message' => 'Donation created successfully'], 201);
        } else {
            $this->response->error('Failed to create donation', 500);
        }
    }

    //Update donation status
    public function updateStatus($id) {
        if (!Auth::isAdmin()) {
            $this->response->error('Unauthorized access', 403);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['status'])) {
            $this->response->error('Status is required', 400);
            return;
        }

        $success = $this->donationModel->updateStatus($id, $data['status']);
        if ($success) {
            $this->response->success(['message' => 'Donation status updated successfully']);
        } else {
            $this->response->error('Failed to update donation status', 500);
        }
    }

    //Get donations statistics
    public function stats() {
        if (!Auth::isAdmin()) {
            $this->response->error('Unauthorized access', 403);
            return;
        }

        $stats = $this->donationModel->getStats();
        $this->response->success(['stats' => $stats]);
    }

    //Get project donations total
    public function projectTotal($projectId) {
        $total = $this->donationModel->getTotalForProject($projectId);
        $this->response->success(['total_donations' => $total]);
    }
}