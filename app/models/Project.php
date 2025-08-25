<?php
namespace App\Models;

use App\Core\Database;
use PDO;

//Project model handling database operations for projects
class Project {
    private \PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    //Get all projects with optional pagination
    public function getAll(int $limit = 10, int $offset = 0): array {
        $sql = "SELECT p.*, u.name as creator_name 
                FROM projects p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.status = 'active' 
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    //Get project by ID
    public function getById(int $id): ?array {
        $sql = "SELECT p.*, u.name as creator_name 
                FROM projects p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch() ?: null;
    }

    //Create new project
    public function create(array $data): int {
        $sql = "INSERT INTO projects 
                (title, description, target_amount, created_by) 
                VALUES (:title, :description, :target_amount, :created_by)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':target_amount' => $data['target_amount'],
            ':created_by' => $data['created_by']
        ]);

        return $this->db->lastInsertId();
    }

    //Update project
    public function update(int $id, array $data): bool {
        $sql = "UPDATE projects 
                SET title = :title, description = :description, 
                    target_amount = :target_amount, status = :status 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':target_amount' => $data['target_amount'],
            ':status' => $data['status'],
            ':id' => $id
        ]);
    }

    //Delete project (soft delete by updating status)
    public function delete(int $id): bool {
        $sql = "UPDATE projects SET status = 'cancelled' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    //Get total count of active projects
    public function getTotalCount(): int {
        $sql = "SELECT COUNT(*) FROM projects WHERE status = 'active'";
        return $this->db->query($sql)->fetchColumn();
    }

    //Update project current amount
    public function updateCurrentAmount(int $id, float $amount): bool {
        $sql = "UPDATE projects 
                SET current_amount = current_amount + :amount 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':amount' => $amount,
            ':id' => $id
        ]);
    }
}