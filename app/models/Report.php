<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Report {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    //Get donations report with filters
    public function getDonationsReport($filters = []) {
        $query = "
            SELECT 
                d.id,
                d.amount,
                d.status,
                d.created_at as donation_date,
                u.name as donor_name,
                u.email as donor_email,
                p.title as project_title,
                p.target_amount as project_target
            FROM donations d
            LEFT JOIN users u ON d.user_id = u.id
            LEFT JOIN projects p ON d.project_id = p.id
            WHERE 1=1
        ";

        $params = [];

        // Add date filters
        if (!empty($filters['start_date'])) {
            $query .= " AND d.created_at >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND d.created_at <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        // Add period filter (last X days)
        if (!empty($filters['period']) && is_numeric($filters['period'])) {
            $query .= " AND d.created_at >= DATE_SUB(NOW(), INTERVAL :period DAY)";
            $params[':period'] = $filters['period'];
        }

        $query .= " ORDER BY d.created_at DESC LIMIT 100";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Get projects report
    public function getProjectsReport($filters = []) {
        $query = "
            SELECT 
                p.id,
                p.title,
                p.description,
                p.target_amount,
                p.current_amount,
                p.status,
                p.created_at,
                p.updated_at,
                u.name as creator_name,
                COUNT(d.id) as donation_count,
                COALESCE(SUM(d.amount), 0) as total_donations
            FROM projects p
            LEFT JOIN users u ON p.created_by = u.id
            LEFT JOIN donations d ON p.id = d.project_id
            WHERE 1=1
        ";

        $params = [];

        // Add date filters
        if (!empty($filters['start_date'])) {
            $query .= " AND p.created_at >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND p.created_at <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $query .= " GROUP BY p.id ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Get users report
    public function getUsersReport($filters = []) {
        $query = "
            SELECT 
                u.id,
                u.name,
                u.email,
                u.role,
                u.created_at,
                u.updated_at,
                COUNT(d.id) as total_donations,
                COALESCE(SUM(d.amount), 0) as total_donation_amount,
                COUNT(p.id) as projects_created
            FROM users u
            LEFT JOIN donations d ON u.id = d.user_id
            LEFT JOIN projects p ON u.id = p.created_by
            WHERE 1=1
        ";

        $params = [];

        // Add date filters
        if (!empty($filters['start_date'])) {
            $query .= " AND u.created_at >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND u.created_at <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $query .= " GROUP BY u.id ORDER BY u.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Get dashboard statistics
    public function getDashboardStats() {
        $stats = [];

        // Total donations
        $stmt = $this->db->query("SELECT COALESCE(SUM(amount), 0) as total_donations FROM donations WHERE status = 'completed'");
        $stats['total_donations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_donations'];

        // Active projects
        $stmt = $this->db->query("SELECT COUNT(*) as active_projects FROM projects WHERE status = 'active'");
        $stats['active_projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['active_projects'];

        // Total users
        $stmt = $this->db->query("SELECT COUNT(*) as total_users FROM users");
        $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

        // Recent donations count
        $stmt = $this->db->query("SELECT COUNT(*) as recent_donations FROM donations WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stats['recent_donations'] = $stmt->fetch(PDO::FETCH_ASSOC)['recent_donations'];

        return $stats;
    }

    //Export report data
    public function exportReport($type, $format, $filters) {
        switch ($type) {
            case 'donations':
                $data = $this->getDonationsReport($filters);
                break;
            case 'projects':
                $data = $this->getProjectsReport($filters);
                break;
            case 'users':
                $data = $this->getUsersReport($filters);
                break;
            default:
                $data = [];
        }

        if ($format === 'csv') {
            return $this->convertToCSV($data);
        } elseif ($format === 'json') {
            return json_encode($data, JSON_PRETTY_PRINT);
        }

        return $data;
    }

    //Convert array to CSV
    private function convertToCSV($data) {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'w');
        
        // Add header
        fputcsv($output, array_keys($data[0]));
        
        // Add data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}