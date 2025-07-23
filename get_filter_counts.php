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
    
    // 4. Price Range - Get the actual min and max prices for combo products
    // Use a simple and reliable approach
    $allPrices = $obj->MysqliSelect1("
        SELECT pp.OfferPrice
        FROM product_price pp
        INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
        WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0
        ORDER BY pp.OfferPrice ASC",
        array("OfferPrice"), "", array());

    // Calculate min/max from the results
    if (!empty($allPrices)) {
        $prices = array_column($allPrices, 'OfferPrice');
        $minPrice = min($prices);
        $maxPrice = max($prices);
    } else {
        $minPrice = 0;
        $maxPrice = 2000;
    }

    // Debug: Log the extracted prices
    error_log("Extracted prices - Min: $minPrice, Max: $maxPrice");

    // Ensure min is actually less than max
    if ($minPrice > $maxPrice) {
        $temp = $minPrice;
        $minPrice = $maxPrice;
        $maxPrice = $temp;
    }

    // Set reasonable defaults if no prices found
    if ($minPrice == 0 && $maxPrice == 0) {
        $minPrice = 0;
        $maxPrice = 2000;
    }

    // Make the price range more inclusive by adding some buffer
    // Round down the minimum to nearest 50 and round up the maximum to nearest 100
    $minPrice = floor($minPrice / 50) * 50;
    $maxPrice = ceil($maxPrice / 100) * 100;

    // Ensure minimum range of at least 500
    if (($maxPrice - $minPrice) < 500) {
        $maxPrice = $minPrice + 500;
    }

    // Debug: Log final price range
    error_log("Final price range - Min: $minPrice, Max: $maxPrice");

    $filterCounts['price_range'] = array(
        'min' => intval($minPrice),
        'max' => intval($maxPrice)
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
