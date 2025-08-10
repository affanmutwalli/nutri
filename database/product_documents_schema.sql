-- =====================================================
-- Product Documents Table Schema
-- =====================================================
-- This table stores PDF documents and other files associated with products
-- such as lab reports, certificates, test reports, etc.

CREATE TABLE IF NOT EXISTS product_documents (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    document_title VARCHAR(255) NOT NULL COMMENT 'Display title for the document',
    document_type ENUM('lab_report', 'certificate', 'test_report', 'specification', 'other') DEFAULT 'lab_report',
    file_name VARCHAR(255) NOT NULL COMMENT 'Original filename',
    file_path VARCHAR(500) NOT NULL COMMENT 'Path to the uploaded file',
    file_size INT NULL COMMENT 'File size in bytes',
    mime_type VARCHAR(100) NULL COMMENT 'MIME type of the file',
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active, 0 = inactive',
    display_order INT DEFAULT 0 COMMENT 'Order for displaying documents',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (product_id) REFERENCES product_master(ProductId) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes for better performance
    INDEX idx_product_id (product_id),
    INDEX idx_document_type (document_type),
    INDEX idx_is_active (is_active),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Table to store PDF documents and files associated with products';

-- =====================================================
-- Create docs directory structure
-- =====================================================
-- Note: The following directories should be created manually:
-- cms/docs/ - Main documents directory
-- cms/docs/products/ - Product-specific documents
-- cms/docs/products/lab_reports/ - Lab reports
-- cms/docs/products/certificates/ - Certificates
-- cms/docs/products/test_reports/ - Test reports
-- cms/docs/products/specifications/ - Specifications
-- cms/docs/products/other/ - Other documents
