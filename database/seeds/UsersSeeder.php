<?php
namespace Database\Seeds;

use App\Core\Database;
use PDO;

//Users table seeder
class UsersSeeder {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    //Run the users seeds
    public function run(): void {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@wfp.org',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@wfp.org',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user'
            ],
            [
                'name' => 'Project Manager',
                'email' => 'manager@wfp.org',
                'password' => password_hash('manager123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ]
        ];

        $sql = "INSERT INTO users (name, email, password, role) 
                VALUES (:name, :email, :password, :role)";

        $stmt = $this->db->prepare($sql);

        foreach ($users as $user) {
            $stmt->execute([
                ':name' => $user['name'],
                ':email' => $user['email'],
                ':password' => $user['password'],
                ':role' => $user['role']
            ]);
        }

        echo "Users seeded successfully!<br>";
    }

    //Truncate users table
    public function truncate(): void {
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->exec("TRUNCATE TABLE users");
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "Users table truncated!<br>";
    }
}