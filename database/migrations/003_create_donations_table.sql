-- Create donations table
CREATE TABLE IF NOT EXISTS donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    project_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'credit_card',
    transaction_id VARCHAR(255) NULL,
    donor_name VARCHAR(255) NOT NULL,
    donor_email VARCHAR(255) NOT NULL,
    donor_phone VARCHAR(50) NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_user_id (user_id),
    INDEX idx_project_id (project_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_transaction_id (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add sample donations data
INSERT INTO donations (user_id, project_id, amount, status, payment_method, donor_name, donor_email, donor_phone, is_anonymous, message) VALUES
(2, 1, 500.00, 'completed', 'credit_card', 'Ahmed Mohamed', 'ahmed@example.com', '+1234567890', FALSE, 'Hope this helps the cause!'),
(2, 2, 250.00, 'completed', 'paypal', 'Fatima Ali', 'fatima@example.com', '+0987654321', FALSE, 'For the children education'),
(3, 1, 1000.00, 'completed', 'bank_transfer', 'Khaled Ibrahim', 'khaled@example.com', '+1122334455', TRUE, NULL),
(2, 3, 750.00, 'completed', 'credit_card', 'Sara Abdullah', 'sara@example.com', '+5566778899', FALSE, 'Clean water for everyone'),
(3, 2, 300.00, 'pending', 'credit_card', 'Mohamed Hassan', 'mohamed@example.com', '+6677889900', FALSE, 'Emergency relief support'),
(2, 1, 200.00, 'completed', 'paypal', 'Layla Mahmoud', 'layla@example.com', '+2233445566', TRUE, NULL),
(3, 3, 1500.00, 'completed', 'bank_transfer', 'Omar Saleh', 'omar@example.com', '+3344556677', FALSE, 'For medical supplies'),
(2, 2, 100.00, 'failed', 'credit_card', 'Noura Khalid', 'noura@example.com', '+4455667788', FALSE, 'Education matters'),
(3, 1, 600.00, 'completed', 'paypal', 'Youssef Ahmed', 'youssef@example.com', '+5566778899', TRUE, NULL),
(2, 3, 400.00, 'refunded', 'credit_card', 'Amira Hassan', 'amira@example.com', '+6677889900', FALSE, 'Water project donation')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;