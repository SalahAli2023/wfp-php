<?php
namespace Database\Seeds;

use App\Core\Database;
use PDO;

//Projects table seeder
class ProjectsSeeder {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    //Run the projects seeds
    public function run(): void {
        $projects = [
            [
                'title' => 'Emergency Food Assistance',
                'description' => 'Providing immediate food assistance to families affected by natural disasters and conflicts in vulnerable regions.',
                'target_amount' => 50000.00,
                'current_amount' => 12500.00,
                'status' => 'active',
                'created_by' => 1
            ],
            [
                'title' => 'School Feeding Program',
                'description' => 'Ensuring children in poor communities receive nutritious meals at school to support their education and health.',
                'target_amount' => 75000.00,
                'current_amount' => 45000.00,
                'status' => 'active',
                'created_by' => 1
            ],
            [
                'title' => 'Agricultural Development',
                'description' => 'Supporting local farmers with training and resources to improve food production and sustainability.',
                'target_amount' => 100000.00,
                'current_amount' => 25000.00,
                'status' => 'active',
                'created_by' => 3
            ],
            [
                'title' => 'Nutrition for Mothers and Children',
                'description' => 'Providing essential nutrients and supplements to pregnant women and children under 5 years old.',
                'target_amount' => 30000.00,
                'current_amount' => 18000.00,
                'status' => 'active',
                'created_by' => 3
            ],
            [
                'title' => 'Clean Water Initiative',
                'description' => 'Building wells and water purification systems in drought-affected areas.',
                'target_amount' => 80000.00,
                'current_amount' => 60000.00,
                'status' => 'active',
                'created_by' => 1
            ]
        ];

        $sql = "INSERT INTO projects 
                (title, description, target_amount, current_amount, status, created_by) 
                VALUES (:title, :description, :target_amount, :current_amount, :status, :created_by)";

        $stmt = $this->db->prepare($sql);

        foreach ($projects as $project) {
            $stmt->execute([
                ':title' => $project['title'],
                ':description' => $project['description'],
                ':target_amount' => $project['target_amount'],
                ':current_amount' => $project['current_amount'],
                ':status' => $project['status'],
                ':created_by' => $project['created_by']
            ]);
        }

        echo "Projects seeded successfully!<br>";
    }

    //Truncate projects table
    public function truncate(): void {
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->exec("TRUNCATE TABLE projects");
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "Projects table truncated!<br>";
    }
}