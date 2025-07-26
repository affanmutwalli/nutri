-- Migration to add image sorting functionality to model_images table
-- Run this script to add sort_order field and update existing records

-- Add sort_order column to model_images table
ALTER TABLE model_images 
ADD COLUMN sort_order INT DEFAULT 0 AFTER PhotoPath;

-- Create index for better performance
CREATE INDEX idx_sort_order ON model_images(ProductId, sort_order);

-- Update existing records to have sequential sort_order based on ImageId
-- This ensures existing images maintain their current order
SET @row_number = 0;
SET @prev_product_id = '';

UPDATE model_images 
SET sort_order = (
    SELECT @row_number := CASE 
        WHEN @prev_product_id = ProductId THEN @row_number + 1 
        ELSE 1 
    END,
    @prev_product_id := ProductId,
    @row_number
)[1]
WHERE (@row_number := 0) = 0 OR (@prev_product_id := '') = ''
ORDER BY ProductId, ImageId;

-- Alternative simpler approach for updating existing records
-- Update each product's images with sequential sort_order
UPDATE model_images m1
JOIN (
    SELECT ImageId, 
           ROW_NUMBER() OVER (PARTITION BY ProductId ORDER BY ImageId) as new_sort_order
    FROM model_images
) m2 ON m1.ImageId = m2.ImageId
SET m1.sort_order = m2.new_sort_order;

-- Verify the migration
SELECT ProductId, ImageId, PhotoPath, sort_order 
FROM model_images 
ORDER BY ProductId, sort_order;
