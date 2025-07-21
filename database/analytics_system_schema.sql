-- =====================================================
-- ANALYTICS SYSTEM DATABASE SCHEMA
-- Cookie-based visitor tracking and product analytics
-- =====================================================

-- =====================================================
-- 1. Visitor Analytics Table
-- =====================================================
CREATE TABLE IF NOT EXISTS visitor_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id VARCHAR(64) NOT NULL COMMENT 'Unique cookie-based visitor identifier',
    customer_id INT NULL COMMENT 'Reference to customer_master.CustomerId if logged in',
    
    -- Session Information
    session_id VARCHAR(128) NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    
    -- Geographic Information
    country VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    
    -- Device Information
    device_type ENUM('desktop', 'mobile', 'tablet', 'unknown') DEFAULT 'unknown',
    browser VARCHAR(100) NULL,
    operating_system VARCHAR(100) NULL,
    
    -- Visit Information
    first_visit TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_visit TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    total_visits INT DEFAULT 1,
    total_page_views INT DEFAULT 0,
    total_session_duration INT DEFAULT 0 COMMENT 'Total time spent in seconds',
    
    -- Conversion Tracking
    has_registered BOOLEAN DEFAULT FALSE,
    has_purchased BOOLEAN DEFAULT FALSE,
    total_orders INT DEFAULT 0,
    total_order_value DECIMAL(10,2) DEFAULT 0.00,
    
    -- Referral Information
    referrer_url TEXT NULL,
    utm_source VARCHAR(100) NULL,
    utm_medium VARCHAR(100) NULL,
    utm_campaign VARCHAR(100) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_visitor (visitor_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_ip_address (ip_address),
    INDEX idx_first_visit (first_visit),
    INDEX idx_last_visit (last_visit),
    INDEX idx_device_type (device_type),
    INDEX idx_has_purchased (has_purchased)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Track unique visitors using cookie-based identification';

-- =====================================================
-- 2. Page Views Table
-- =====================================================
CREATE TABLE IF NOT EXISTS page_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id VARCHAR(64) NOT NULL,
    customer_id INT NULL,
    
    -- Page Information
    page_url VARCHAR(500) NOT NULL,
    page_title VARCHAR(200) NULL,
    page_type ENUM('home', 'product', 'category', 'cart', 'checkout', 'other') DEFAULT 'other',
    
    -- Product-specific tracking
    product_id INT NULL COMMENT 'If viewing a product page',
    category_id INT NULL COMMENT 'If viewing a category page',
    
    -- Session Information
    session_id VARCHAR(128) NULL,
    
    -- Interaction Data
    time_on_page INT NULL COMMENT 'Time spent on page in seconds',
    scroll_depth DECIMAL(5,2) NULL COMMENT 'Percentage of page scrolled',
    
    -- Technical Information
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    referrer_url TEXT NULL,
    
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_product_id (product_id),
    INDEX idx_category_id (category_id),
    INDEX idx_page_type (page_type),
    INDEX idx_viewed_at (viewed_at),
    INDEX idx_session_id (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Track individual page views and user interactions';

-- =====================================================
-- 3. Product Analytics Table
-- =====================================================
CREATE TABLE IF NOT EXISTS product_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    
    -- View Metrics
    total_views INT DEFAULT 0,
    unique_views INT DEFAULT 0,
    
    -- Interaction Metrics
    total_cart_additions INT DEFAULT 0,
    unique_cart_additions INT DEFAULT 0,
    total_purchases INT DEFAULT 0,
    total_purchase_quantity INT DEFAULT 0,
    total_revenue DECIMAL(12,2) DEFAULT 0.00,
    
    -- Conversion Rates (calculated fields)
    view_to_cart_rate DECIMAL(5,2) DEFAULT 0.00,
    cart_to_purchase_rate DECIMAL(5,2) DEFAULT 0.00,
    overall_conversion_rate DECIMAL(5,2) DEFAULT 0.00,
    
    -- Time-based metrics
    average_time_on_product_page INT DEFAULT 0 COMMENT 'Average time in seconds',
    
    -- Last updated
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_product (product_id),
    INDEX idx_total_views (total_views),
    INDEX idx_total_purchases (total_purchases),
    INDEX idx_total_revenue (total_revenue),
    INDEX idx_conversion_rate (overall_conversion_rate),
    
    FOREIGN KEY (product_id) REFERENCES product_master(ProductId) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Aggregate analytics data for each product';

-- =====================================================
-- 4. User Actions Table
-- =====================================================
CREATE TABLE IF NOT EXISTS user_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id VARCHAR(64) NOT NULL,
    customer_id INT NULL,
    
    -- Action Information
    action_type ENUM('page_view', 'product_view', 'add_to_cart', 'remove_from_cart', 'purchase', 'search', 'filter', 'click', 'scroll') NOT NULL,
    action_details JSON NULL COMMENT 'Additional action-specific data',
    
    -- Target Information
    target_type ENUM('product', 'category', 'page', 'button', 'link', 'other') NULL,
    target_id INT NULL COMMENT 'ID of the target (product_id, category_id, etc.)',
    target_name VARCHAR(200) NULL,
    
    -- Context Information
    page_url VARCHAR(500) NULL,
    session_id VARCHAR(128) NULL,
    
    -- Value Information
    action_value DECIMAL(10,2) NULL COMMENT 'Monetary value if applicable',
    quantity INT NULL COMMENT 'Quantity if applicable',
    
    -- Technical Information
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_action_type (action_type),
    INDEX idx_target_type (target_type),
    INDEX idx_target_id (target_id),
    INDEX idx_created_at (created_at),
    INDEX idx_session_id (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Track all user actions and interactions';

-- =====================================================
-- 5. Daily Analytics Summary Table
-- =====================================================
CREATE TABLE IF NOT EXISTS daily_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    analytics_date DATE NOT NULL,
    
    -- Visitor Metrics
    total_visitors INT DEFAULT 0,
    new_visitors INT DEFAULT 0,
    returning_visitors INT DEFAULT 0,
    
    -- Page View Metrics
    total_page_views INT DEFAULT 0,
    unique_page_views INT DEFAULT 0,
    average_pages_per_session DECIMAL(5,2) DEFAULT 0.00,
    
    -- Session Metrics
    total_sessions INT DEFAULT 0,
    average_session_duration INT DEFAULT 0 COMMENT 'Average duration in seconds',
    bounce_rate DECIMAL(5,2) DEFAULT 0.00,
    
    -- Conversion Metrics
    total_registrations INT DEFAULT 0,
    total_orders INT DEFAULT 0,
    total_revenue DECIMAL(12,2) DEFAULT 0.00,
    conversion_rate DECIMAL(5,2) DEFAULT 0.00,
    
    -- Product Metrics
    most_viewed_product_id INT NULL,
    most_purchased_product_id INT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_date (analytics_date),
    INDEX idx_analytics_date (analytics_date),
    INDEX idx_total_visitors (total_visitors),
    INDEX idx_total_revenue (total_revenue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Daily aggregated analytics data for reporting';

-- =====================================================
-- 6. Search Analytics Table
-- =====================================================
CREATE TABLE IF NOT EXISTS search_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id VARCHAR(64) NOT NULL,
    customer_id INT NULL,
    
    -- Search Information
    search_query VARCHAR(500) NOT NULL,
    search_type ENUM('product', 'category', 'general') DEFAULT 'general',
    results_count INT DEFAULT 0,
    
    -- Interaction Information
    clicked_result_position INT NULL COMMENT 'Position of clicked result (1-based)',
    clicked_product_id INT NULL,
    
    -- Context Information
    page_url VARCHAR(500) NULL,
    session_id VARCHAR(128) NULL,
    
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_search_query (search_query),
    INDEX idx_clicked_product_id (clicked_product_id),
    INDEX idx_searched_at (searched_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Track search queries and interactions';

-- =====================================================
-- Create Views for Easy Reporting
-- =====================================================

-- Popular Products View
CREATE OR REPLACE VIEW popular_products AS
SELECT 
    pa.product_id,
    pm.ProductName,
    pm.PhotoPath,
    pa.total_views,
    pa.unique_views,
    pa.total_cart_additions,
    pa.total_purchases,
    pa.total_revenue,
    pa.overall_conversion_rate,
    RANK() OVER (ORDER BY pa.total_views DESC) as view_rank,
    RANK() OVER (ORDER BY pa.total_purchases DESC) as purchase_rank
FROM product_analytics pa
JOIN product_master pm ON pa.product_id = pm.ProductId
WHERE pa.total_views > 0
ORDER BY pa.total_views DESC;

-- Visitor Summary View
CREATE OR REPLACE VIEW visitor_summary AS
SELECT 
    DATE(first_visit) as visit_date,
    COUNT(*) as total_visitors,
    COUNT(CASE WHEN total_visits = 1 THEN 1 END) as new_visitors,
    COUNT(CASE WHEN total_visits > 1 THEN 1 END) as returning_visitors,
    AVG(total_page_views) as avg_pages_per_visitor,
    AVG(total_session_duration) as avg_session_duration,
    COUNT(CASE WHEN has_purchased = TRUE THEN 1 END) as converted_visitors,
    SUM(total_order_value) as total_revenue
FROM visitor_analytics
GROUP BY DATE(first_visit)
ORDER BY visit_date DESC;

-- =====================================================
-- Create Indexes for Performance
-- =====================================================

-- Additional composite indexes for common queries
CREATE INDEX idx_visitor_customer_date ON page_views(visitor_id, customer_id, viewed_at);
CREATE INDEX idx_product_action_date ON user_actions(target_id, action_type, created_at);
CREATE INDEX idx_visitor_session ON user_actions(visitor_id, session_id);

-- =====================================================
-- Insert Initial Data
-- =====================================================

-- Initialize product analytics for existing products
INSERT IGNORE INTO product_analytics (product_id)
SELECT ProductId FROM product_master;

SELECT 'Analytics system database schema created successfully!' as result;
