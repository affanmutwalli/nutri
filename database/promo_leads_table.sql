-- Create table for promotional popup leads
CREATE TABLE IF NOT EXISTS promo_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mobile_number VARCHAR(15) NOT NULL,
    otp VARCHAR(6) NULL,
    otp_generated_at TIMESTAMP NULL,
    is_verified TINYINT(1) DEFAULT 0 COMMENT '0=not verified, 1=verified',
    verified_at TIMESTAMP NULL,
    promo_code_used TINYINT(1) DEFAULT 0 COMMENT '0=not used, 1=used',
    promo_code_used_at TIMESTAMP NULL,
    source VARCHAR(50) DEFAULT 'promotional_popup' COMMENT 'Source of the lead',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_mobile (mobile_number),
    INDEX idx_verified (is_verified),
    INDEX idx_created_at (created_at),
    INDEX idx_otp_generated (otp_generated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Store promotional popup leads and OTP verification data';

-- Insert sample data for testing (optional)
-- INSERT INTO promo_leads (mobile_number, is_verified, verified_at, source) 
-- VALUES ('8208593432', 1, NOW(), 'promotional_popup');
