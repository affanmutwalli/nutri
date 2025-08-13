-- Product About Management System Database Schema
-- This creates tables for managing "About Product" content in CMS

-- Main table for product about sections
CREATE TABLE IF NOT EXISTS product_about_sections (
    section_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    section_title VARCHAR(255) NOT NULL COMMENT 'Title of the about section (e.g., Key Benefits, How to Use)',
    section_content TEXT NOT NULL COMMENT 'HTML content for the section',
    section_type ENUM('benefits', 'usage', 'ingredients', 'specifications', 'safety', 'faq', 'custom') DEFAULT 'custom' COMMENT 'Type of section for categorization',
    display_order INT DEFAULT 0 COMMENT 'Order for displaying sections (lower numbers first)',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active (show on frontend), 0 = inactive',
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_product_sections (product_id, is_active, display_order),
    INDEX idx_section_type (section_type),
    
    -- Foreign key constraint
    CONSTRAINT fk_about_sections_product 
        FOREIGN KEY (product_id) REFERENCES product_master(ProductId) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores about product sections content';

-- Table for section-related images (future enhancement)
CREATE TABLE IF NOT EXISTS product_about_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    image_title VARCHAR(255) NULL COMMENT 'Optional title for the image',
    image_path VARCHAR(500) NOT NULL COMMENT 'Path to the uploaded image',
    image_alt_text VARCHAR(255) NULL COMMENT 'Alt text for accessibility',
    display_order INT DEFAULT 0 COMMENT 'Order for displaying images within a section',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active, 0 = inactive',
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_section_images (section_id, is_active, display_order),
    
    -- Foreign key constraint
    CONSTRAINT fk_about_images_section 
        FOREIGN KEY (section_id) REFERENCES product_about_sections(section_id) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores images for about sections';

-- Table for bullet points within sections (future enhancement)
CREATE TABLE IF NOT EXISTS product_about_points (
    point_id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    point_title VARCHAR(255) NULL COMMENT 'Optional title for the point',
    point_content TEXT NOT NULL COMMENT 'Content of the point',
    point_icon VARCHAR(100) NULL COMMENT 'Optional icon class (e.g., fas fa-check) or image path',
    display_order INT DEFAULT 0 COMMENT 'Order for displaying points within a section',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active, 0 = inactive',
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_section_points (section_id, is_active, display_order),
    
    -- Foreign key constraint
    CONSTRAINT fk_about_points_section 
        FOREIGN KEY (section_id) REFERENCES product_about_sections(section_id) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores bullet points for about sections';

-- Create a view for easy querying of complete about information
CREATE OR REPLACE VIEW product_about_complete AS
SELECT 
    s.section_id,
    s.product_id,
    s.section_title,
    s.section_content,
    s.section_type,
    s.display_order,
    s.is_active,
    s.created_date,
    s.updated_date,
    p.ProductName,
    p.ProductCode,
    COUNT(i.image_id) as image_count,
    COUNT(pt.point_id) as point_count
FROM product_about_sections s
LEFT JOIN product_master p ON s.product_id = p.ProductId
LEFT JOIN product_about_images i ON s.section_id = i.section_id AND i.is_active = 1
LEFT JOIN product_about_points pt ON s.section_id = pt.section_id AND pt.is_active = 1
GROUP BY s.section_id, s.product_id, s.section_title, s.section_content, s.section_type, 
         s.display_order, s.is_active, s.created_date, s.updated_date, p.ProductName, p.ProductCode
ORDER BY s.product_id, s.display_order, s.section_id;

-- Insert sample data for testing (ProductId = 6 - Wild Amla Juice)
INSERT IGNORE INTO product_about_sections (product_id, section_title, section_content, section_type, display_order, is_active) VALUES
(6, 'About Our Wild Amla Juice', 
 '<p>This nutrient-dense Wild Amla Juice is derived from naturally grown, hand-harvested wild Indian gooseberries, a rich source of Vitamin C, polyphenols, and flavonoids. Known in Ayurveda as a powerful rasayana (rejuvenator), amla delivers potent antioxidants that neutralize free radicals, reduce oxidative stress, and promote cardiovascular and digestive health.</p><p>Its high vitamin and mineral profile supports glowing skin, healthy hair, and strong nails, while also enhancing liver detoxification and metabolic efficiency. Cold-pressed for maximum nutrient retention, this juice offers a wholesome, preservative-free way to nourish your body from within.</p>', 
 'benefits', 1, 1);

-- Show success message
SELECT 'Product About Schema Created Successfully!' as message;
