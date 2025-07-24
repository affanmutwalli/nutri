-- =====================================================
-- Product Offers System Database Schema
-- Creates table to manage which products are featured as offers
-- =====================================================

-- =====================================================
-- Product Offers Table
-- =====================================================
CREATE TABLE IF NOT EXISTS product_offers (
    offer_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    offer_title VARCHAR(255) NULL COMMENT 'Optional custom title for the offer',
    offer_description TEXT NULL COMMENT 'Optional description for the offer',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active offer, 0 = inactive',
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint to ensure product exists
    FOREIGN KEY (product_id) REFERENCES product_master(ProductId) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Unique constraint to ensure each product can only have one offer entry
    UNIQUE KEY unique_product_offer (product_id),
    
    -- Index for faster queries
    INDEX idx_is_active (is_active),
    INDEX idx_created_date (created_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Table to manage which products are featured as special offers';

-- =====================================================
-- Create a view for easy querying of offers with product details
-- =====================================================
CREATE OR REPLACE VIEW active_product_offers AS
SELECT 
    po.offer_id,
    po.product_id,
    po.offer_title,
    po.offer_description,
    po.created_date,
    po.updated_date,
    pm.ProductName,
    pm.PhotoPath,
    pm.ShortDescription,
    pm.CategoryId,
    pm.SubCategoryId,
    pm.ProductCode,
    pm.Specification,
    pm.MetaTags,
    pm.MetaKeywords,
    -- Get the minimum offer price and corresponding MRP for savings calculation
    MIN(pp.OfferPrice) as min_offer_price,
    MIN(pp.MRP) as min_mrp,
    (MIN(pp.MRP) - MIN(pp.OfferPrice)) as savings_amount,
    ROUND(((MIN(pp.MRP) - MIN(pp.OfferPrice)) / MIN(pp.MRP)) * 100, 0) as discount_percentage
FROM product_offers po
INNER JOIN product_master pm ON po.product_id = pm.ProductId
INNER JOIN product_price pp ON pm.ProductId = pp.ProductId
WHERE po.is_active = 1
GROUP BY po.offer_id, po.product_id, po.offer_title, po.offer_description, 
         po.created_date, po.updated_date, pm.ProductName, pm.PhotoPath, 
         pm.ShortDescription, pm.CategoryId, pm.SubCategoryId, pm.ProductCode,
         pm.Specification, pm.MetaTags, pm.MetaKeywords;

-- =====================================================
-- Insert some sample data for testing (optional)
-- =====================================================
-- Uncomment the lines below if you want to add sample offers for testing
-- Note: Make sure the ProductId values exist in your product_master table

/*
INSERT INTO product_offers (product_id, offer_title, offer_description, is_active) VALUES
(1, 'Special Launch Offer', 'Limited time offer on our bestselling product', 1),
(2, 'Weekend Deal', 'Great savings this weekend only', 1),
(3, 'Bulk Purchase Discount', 'Save more when you buy in bulk', 1)
ON DUPLICATE KEY UPDATE 
    offer_title = VALUES(offer_title),
    offer_description = VALUES(offer_description),
    is_active = VALUES(is_active),
    updated_date = CURRENT_TIMESTAMP;
*/

SELECT 'Product offers database schema created successfully!' as result;
