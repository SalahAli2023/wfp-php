<?php
namespace Database\Seeds;

use App\Core\Database;
use PDO;

//Donations table seeder
class DonationsSeeder {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    //Run the donations seeds
    public function run(): void {
        $donations = [
            [
                'user_id' => 2,
                'project_id' => 1,
                'amount' => 500.00,
                'status' => 'completed',
                'payment_method' => 'credit_card',
                'donor_name' => 'Ahmed Mohamed',
                'donor_email' => 'ahmed@example.com',
                'donor_phone' => '+1234567890',
                'is_anonymous' => false,
                'message' => 'Hope this helps the cause!'
            ],
            [
                'user_id' => 2,
                'project_id' => 2,
                'amount' => 250.00,
                'status' => 'completed',
                'payment_method' => 'paypal',
                'donor_name' => 'Fatima Ali',
                'donor_email' => 'fatima@example.com',
                'donor_phone' => '+0987654321',
                'is_anonymous' => false,
                'message' => 'For the children education'
            ],
            [
                'user_id' => 3,
                'project_id' => 1,
                'amount' => 1000.00,
                'status' => 'completed',
                'payment_method' => 'bank_transfer',
                'donor_name' => 'Khaled Ibrahim',
                'donor_email' => 'khaled@example.com',
                'donor_phone' => '+1122334455',
                'is_anonymous' => true,
                'message' => null
            ],
            [
                'user_id' => 2,
                'project_id' => 3,
                'amount' => 750.00,
                'status' => 'completed',
                'payment_method' => 'credit_card',
                'donor_name' => 'Sara Abdullah',
                'donor_email' => 'sara@example.com',
                'donor_phone' => '+5566778899',
                'is_anonymous' => false,
                'message' => 'Clean water for everyone'
            ],
            [
                'user_id' => 3,
                'project_id' => 2,
                'amount' => 300.00,
                'status' => 'pending',
                'payment_method' => 'credit_card',
                'donor_name' => 'Mohamed Hassan',
                'donor_email' => 'mohamed@example.com',
                'donor_phone' => '+6677889900',
                'is_anonymous' => false,
                'message' => 'Emergency relief support'
            ]
        ];

        $sql = "INSERT INTO donations 
                (user_id, project_id, amount, status, payment_method, donor_name, donor_email, donor_phone, is_anonymous, message) 
                VALUES 
                (:user_id, :project_id, :amount, :status, :payment_method, :donor_name, :donor_email, :donor_phone, :is_anonymous, :message)";

        $stmt = $this->db->prepare($sql);

        foreach ($donations as $donation) {
            $stmt->execute([
                ':user_id' => $donation['user_id'],
                ':project_id' => $donation['project_id'],
                ':amount' => $donation['amount'],
                ':status' => $donation['status'],
                ':payment_method' => $donation['payment_method'],
                ':donor_name' => $donation['donor_name'],
                ':donor_email' => $donation['donor_email'],
                ':donor_phone' => $donation['donor_phone'],
                ':is_anonymous' => $donation['is_anonymous'],
                ':message' => $donation['message']
            ]);
        }

        echo "Donations seeded successfully!<br>";
    }

    //Truncate donations table
    public function truncate(): void {
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->exec("TRUNCATE TABLE donations");
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "Donations table truncated!<br>";
    }
}