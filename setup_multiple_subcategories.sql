-- Setup Multiple Sub-Categories for Products
-- This allows products to belong to multiple sub-categories

-- Create junction table for product-subcategory relationships
CREATE TABLE IF NOT EXISTS product_subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ProductId INT NOT NULL,
    SubCategoryId INT NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE COMMENT 'Indicates the primary/main subcategory',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Ensure unique combinations
    UNIQUE KEY unique_product_subcategory (ProductId, SubCategoryId),
    
    -- Foreign key constraints
    FOREIGN KEY (ProductId) REFERENCES product_master(ProductId) ON DELETE CASCADE,
    FOREIGN KEY (SubCategoryId) REFERENCES sub_category(SubCategoryId) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_product_id (ProductId),
    INDEX idx_subcategory_id (SubCategoryId),
    INDEX idx_primary (is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrate existing data from product_master to junction table
INSERT IGNORE INTO product_subcategories (ProductId, SubCategoryId, is_primary)
SELECT ProductId, SubCategoryId, TRUE
FROM product_master 
WHERE SubCategoryId IS NOT NULL;

-- Show migration results
SELECT 'Migration completed. Records inserted:' as status;
SELECT COUNT(*) as migrated_records FROM product_subcategories;

-- Show sample data
SELECT 'Sample migrated data:' as info;
SELECT ps.ProductId, pm.ProductName, ps.SubCategoryId, sc.SubCategoryName, ps.is_primary
FROM product_subcategories ps
JOIN product_master pm ON ps.ProductId = pm.ProductId
JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
ORDER BY ps.ProductId
LIMIT 10;

-- Create view for easy querying
CREATE OR REPLACE VIEW product_with_subcategories AS
SELECT 
    pm.ProductId,
    pm.ProductName,
    pm.PhotoPath,
    pm.IsCombo,
    pm.CategoryId,
    pm.ProductCode,
    pm.ShortDescription,
    pm.Specification,
    pm.MetaTags,
    pm.MetaKeywords,
    pm.Title,
    pm.Description,
    pm.VideoURL,
    GROUP_CONCAT(ps.SubCategoryId) as SubCategoryIds,
    GROUP_CONCAT(sc.SubCategoryName) as SubCategoryNames,
    (SELECT SubCategoryId FROM product_subcategories WHERE ProductId = pm.ProductId AND is_primary = TRUE LIMIT 1) as PrimarySubCategoryId
FROM product_master pm
LEFT JOIN product_subcategories ps ON pm.ProductId = ps.ProductId
LEFT JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
GROUP BY pm.ProductId;

-- Show setup completion
SELECT 'Setup completed successfully!' as status;
SELECT 'Next steps:' as info;
SELECT '1. Update CMS product form to use multi-select for sub-categories' as step1;
SELECT '2. Update products.php to query the junction table' as step2;
SELECT '3. Update product save logic to handle multiple sub-categories' as step3;
