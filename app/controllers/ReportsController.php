<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Response;
use App\Models\Report;

class ReportsController {
    private $response;
    private $reportModel;

    public function __construct() {
        $this->response = new Response();
        $this->reportModel = new Report();
    }

    public function getDonationsReport() {
        // Check if user is admin
        if (!Auth::isAdmin()) {
            $this->response->error('Unauthorized access. Admin only.', 403);
            return;
        }

        $filters = $this->getFilters();
        $data = $this->reportModel->getDonationsReport($filters);
        
        $this->response->success([
            'data' => $data,
            'filters' => $filters,
            'total' => count($data)
        ]);
    }

    public function getProjectsReport() {
        if (!Auth::isAdmin()) {
            $this->response->error('Unauthorized', 403);
            return;
        }

        $filters = $this->getFilters();
        $data = $this->reportModel->getProjectsReport($filters);
        
        $this->response->success([
            'data' => $data,
            'filters' => $filters,
            'total' => count($data)
        ]);
    }

    public function getUsersReport() {
        if (!Auth::isAdmin()) {
            $this->response->error('Unauthorized', 403);
            return;
        }

        $filters = $this->getFilters();
        $data = $this->reportModel->getUsersReport($filters);
        
        $this->response->success([
            'data' => $data,
            'filters' => $filters,
            'total' => count($data)
        ]);
    }

    public function getDashboardStats() {
        if (!Auth::isAdmin()) {
            $this->response->error('Unauthorized access', 403);
            return;
        }

        $stats = $this->reportModel->getDashboardStats();
        $this->response->success($stats);
    }

    public function exportReport($type) {
        if (!Auth::isAdmin()) {
            $this->response->error('Unauthorized access', 403);
            return;
        }

        $format = $_GET['format'] ?? 'csv';
        $filters = $this->getFilters();

        $data = $this->reportModel->exportReport($type, $format, $filters);
        
        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $type . '_report.csv"');
            echo $data;
        } elseif ($format === 'json') {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $type . '_report.json"');
            echo $data;
        } else {
            $this->response->success($data);
        }
        exit;
    }

    private function getFilters() {
        return [
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null,
            'period' => $_GET['period'] ?? '30'
        ];
    }
}