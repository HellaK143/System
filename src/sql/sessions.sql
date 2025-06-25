CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    session_type ENUM('interview','pitch') NOT NULL,
    scheduled_by INT NOT NULL,
    scheduled_for INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    notes TEXT,
    status ENUM('scheduled','completed','cancelled') DEFAULT 'scheduled',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (scheduled_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (scheduled_for) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_app (application_id),
    INDEX idx_for (scheduled_for)
); 