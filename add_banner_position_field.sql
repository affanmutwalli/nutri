-- Add Position field to banners table for sorting functionality
-- This script safely adds the Position column if it doesn't already exist

-- Check if Position column exists and add it if it doesn't
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'banners' 
AND COLUMN_NAME = 'Position';

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE banners ADD COLUMN Position INT DEFAULT 0 COMMENT "Display order for banners (0 = highest priority)"',
    'SELECT "Position column already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing banners to have sequential positions if they're all 0
UPDATE banners 
SET Position = (
    SELECT @row_number := @row_number + 1 
    FROM (SELECT @row_number := -1) r
) 
WHERE Position = 0 
AND (SELECT COUNT(*) FROM (SELECT * FROM banners) b WHERE Position > 0) = 0;

-- Show the current banner order
SELECT 
    BannerId,
    Title,
    Position,
    CASE WHEN ShowButton = 1 THEN 'Enabled' ELSE 'Disabled' END as ShowButton,
    PhotoPath
FROM banners 
ORDER BY Position ASC, BannerId ASC;
