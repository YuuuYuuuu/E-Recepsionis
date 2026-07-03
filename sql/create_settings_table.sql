-- Create settings table for application configuration
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value LONGTEXT,
    setting_type VARCHAR(50) DEFAULT 'string',
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default thumbnail settings
INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, description) VALUES
('thumbnail_height', '180', 'number', 'Tinggi thumbnail preview (px)'),
('thumbnail_border_radius', '12', 'number', 'Border radius thumbnail (px)'),
('thumbnail_bg_color', '#e2e8f0', 'color', 'Warna background placeholder thumbnail'),
('thumbnail_margin_bottom', '15', 'number', 'Margin bawah thumbnail (px)');
