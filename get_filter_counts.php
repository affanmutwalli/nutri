<?php
session_start();
include('database/dbconnection.php');

// Set content type to JSON
header('Content-Type: application/json');

// Initialize database connection
$obj = new main();
$obj->connection();

try {
    $filterCounts = array();
    
    // 1. Product Type Counts
    $filterCounts['product_type'] = array();
    
    // Combo products count
    $comboCount = $obj->MysqliSelect1("SELECT COUNT(*) as count FROM product_master WHERE IsCombo = 'Y'", 
        array("count"), "", array());
    $filterCounts['product_type']['combos'] = $comboCount[0]['count'] ?? 0;
    
    // Cosmetics count (products with cosmetics-related categories)
    $cosmeticsCount = $obj->MysqliSelect1("
        SELECT COUNT(DISTINCT pm.ProductId) as count 
        FROM product_master pm 
        LEFT JOIN category_master cm ON pm.CategoryId = cm.CategoryId 
        WHERE pm.IsCombo = 'Y' AND (cm.CategoryName LIKE '%cosmetic%' OR cm.CategoryName LIKE '%beauty%' OR cm.CategoryName LIKE '%skin%')", 
        array("count"), "", array());
    $filterCounts['product_type']['cosmetics'] = $cosmeticsCount[0]['count'] ?? 0;
    
    // Herbal Powders count
    $herbalCount = $obj->MysqliSelect1("
        SELECT COUNT(DISTINCT pm.ProductId) as count 
        FROM product_master pm 
        LEFT JOIN category_master cm ON pm.CategoryId = cm.CategoryId 
        WHERE pm.IsCombo = 'Y' AND (cm.CategoryName LIKE '%powder%' OR cm.CategoryName LIKE '%churna%' OR cm.CategoryName LIKE '%herbal%')", 
        array("count"), "", array());
    $filterCounts['product_type']['herbal-powders'] = $herbalCount[0]['count'] ?? 0;
    
    // 2. Category Counts
    $filterCounts['categories'] = array();

    $categoriesQuery = "
        SELECT cm.CategoryId, cm.CategoryName, COUNT(pm.ProductId) as count
        FROM category_master cm
        INNER JOIN product_master pm ON cm.CategoryId = pm.CategoryId
        WHERE pm.IsCombo = 'Y'
        GROUP BY cm.CategoryId, cm.CategoryName
        ORDER BY count DESC";

    $categories = $obj->MysqliSelect1($categoriesQuery,
        array("CategoryId", "CategoryName", "count"), "", array());

    if (!empty($categories)) {
        foreach ($categories as $cat) {
            $filterCounts['categories'][] = array(
                'id' => $cat['CategoryId'],
                'name' => $cat['CategoryName'],
                'count' => $cat['count']
            );
        }
    }

    // 3. Subcategory Counts
    $filterCounts['subcategories'] = array();

    // Check if multiple subcategories system is being used
    $multiSubQuery = "SELECT COUNT(*) as count FROM product_subcategories ps INNER JOIN product_master pm ON ps.ProductId = pm.ProductId WHERE pm.IsCombo = 'Y'";
    $multiSubResult = $obj->MysqliSelect1($multiSubQuery, array("count"), "", array());
    $useMultipleSubcategories = ($multiSubResult[0]['count'] ?? 0) > 0;

    if ($useMultipleSubcategories) {
        // Use junction table for subcategories
        $subcategoriesQuery = "
            SELECT sc.SubCategoryId, sc.SubCategoryName, COUNT(DISTINCT pm.ProductId) as count
            FROM product_subcategories ps
            INNER JOIN product_master pm ON ps.ProductId = pm.ProductId
            INNER JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
            WHERE pm.IsCombo = 'Y'
            GROUP BY sc.SubCategoryId, sc.SubCategoryName
            ORDER BY count DESC";
    } else {
        // Use direct subcategory relationship
        $subcategoriesQuery = "
            SELECT sc.SubCategoryId, sc.SubCategoryName, COUNT(pm.ProductId) as count
            FROM sub_category sc
            INNER JOIN product_master pm ON sc.SubCategoryId = pm.SubCategoryId
            WHERE pm.IsCombo = 'Y'
            GROUP BY sc.SubCategoryId, sc.SubCategoryName
            ORDER BY count DESC";
    }

    $subcategories = $obj->MysqliSelect1($subcategoriesQuery,
        array("SubCategoryId", "SubCategoryName", "count"), "", array());

    if (!empty($subcategories)) {
        foreach ($subcategories as $subcat) {
            $filterCounts['subcategories'][] = array(
                'id' => $subcat['SubCategoryId'],
                'name' => $subcat['SubCategoryName'],
                'count' => $subcat['count']
            );
        }
    }
    
    // 3. Availability Counts
    $filterCounts['availability'] = array();
    
    // In stock (all combo products for now, since we don't track stock)
    $inStockCount = $obj->MysqliSelect1("SELECT COUNT(*) as count FROM product_master WHERE IsCombo = 'Y'", 
        array("count"), "", array());
    $filterCounts['availability']['in-stock'] = $inStockCount[0]['count'] ?? 0;
    $filterCounts['availability']['out-of-stock'] = 0; // No out of stock tracking yet
    
    // 4. Price Range
    $priceRange = $obj->MysqliSelect1("
        SELECT 
            MIN(pp.OfferPrice) as min_price,
            MAX(pp.OfferPrice) as max_price
        FROM product_price pp 
        INNER JOIN product_master pm ON pp.ProductId = pm.ProductId 
        WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0", 
        array("min_price", "max_price"), "", array());
    
    $filterCounts['price_range'] = array(
        'min' => $priceRange[0]['min_price'] ?? 0,
        'max' => $priceRange[0]['max_price'] ?? 2000
    );
    
    // 5. Total product count
    $totalCount = $obj->MysqliSelect1("SELECT COUNT(*) as count FROM product_master WHERE IsCombo = 'Y'", 
        array("count"), "", array());
    $filterCounts['total_products'] = $totalCount[0]['count'] ?? 0;
    
    // Return JSON response
    echo json_encode(array(
        'success' => true,
        'filter_counts' => $filterCounts
    ));
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'error' => $e->getMessage()
    ));
}
?>
