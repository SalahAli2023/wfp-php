<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Donation {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    //Get all donations with optional filters
    public function getAll($filters = []) {
        $query = "SELECT d.*, p.title as project_title, u.name as user_name 
                 FROM donations d 
                 LEFT JOIN projects p ON d.project_id = p.id 
                 LEFT JOIN users u ON d.user_id = u.id 
                 WHERE 1=1";

        $params = [];

        if (!empty($filters['project_id'])) {
            $query .= " AND d.project_id = :project_id";
            $params[':project_id'] = $filters['project_id'];
        }

        if (!empty($filters['user_id'])) {
            $query .= " AND d.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        if (!empty($filters['status'])) {
            $query .= " AND d.status = :status";
            $params[':status'] = $filters['status'];
        }

        $query .= " ORDER BY d.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Get donation by ID
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT d.*, p.title as project_title, u.name as user_name 
            FROM donations d 
            LEFT JOIN projects p ON d.project_id = p.id 
            LEFT JOIN users u ON d.user_id = u.id 
            WHERE d.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Create new donation
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO donations (user_id, project_id, amount, status, payment_method, donor_name, donor_email) 
            VALUES (:user_id, :project_id, :amount, :status, :payment_method, :donor_name, :donor_email)
        ");

        return $stmt->execute([
            ':user_id' => $data['user_id'],
            ':project_id' => $data['project_id'],
            ':amount' => $data['amount'],
            ':status' => $data['status'] ?? 'pending',
            ':payment_method' => $data['payment_method'] ?? 'credit_card',
            ':donor_name' => $data['donor_name'],
            ':donor_email' => $data['donor_email']
        ]);
    }

    //Update donation status
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("
            UPDATE donations SET status = :status, updated_at = NOW() WHERE id = :id
        ");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    //Get total donations amount for a project
    public function getTotalForProject($projectId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM donations 
            WHERE project_id = :project_id AND status = 'completed'
        ");
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    //Get donations statistics
    public function getStats() {
        $stats = [];

        // Total donations
        $stmt = $this->db->query("SELECT COALESCE(SUM(amount), 0) as total_donations FROM donations WHERE status = 'completed'");
        $stats['total_donations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_donations'];

        // Total donors
        $stmt = $this->db->query("SELECT COUNT(DISTINCT user_id) as total_donors FROM donations");
        $stats['total_donors'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_donors'];

        // Recent donations (last 7 days)
        $stmt = $this->db->query("SELECT COUNT(*) as recent_donations FROM donations WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stats['recent_donations'] = $stmt->fetch(PDO::FETCH_ASSOC)['recent_donations'];

        return $stats;
    }
}