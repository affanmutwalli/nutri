<?php
session_start();
include('database/dbconnection.php');

// Set content type to JSON
header('Content-Type: application/json');

// Initialize database connection
$obj = new main();
$obj->connection();

try {
    // Get filter parameters from POST request
    $filters = json_decode(file_get_contents('php://input'), true);

    // If no filters provided, use defaults
    if (!$filters) {
        $filters = array(
            'product_type' => array('combos'),
            'sort' => 'featured'
        );
    }

    // Start with the simplest possible query - just get all combo products
    $query = "SELECT pm.ProductId, pm.ProductName, pm.PhotoPath, pm.ShortDescription, pm.CategoryId, pm.SubCategoryId, pm.IsCombo
              FROM product_master pm
              WHERE pm.IsCombo = 'Y'";

    $params = array();
    $paramTypes = "";

    // Only apply additional filters if they're actually restrictive

    // Price filter - only if user has moved the sliders significantly
    if (isset($filters['price_min']) && isset($filters['price_max'])) {
        $priceMin = floatval($filters['price_min']);
        $priceMax = floatval($filters['price_max']);

        // Only filter if it's not the default full range
        if ($priceMin > 0 || $priceMax < 5000) {
            $query .= " AND EXISTS (
                SELECT 1 FROM product_price pp
                WHERE pp.ProductId = pm.ProductId
                AND pp.OfferPrice BETWEEN ? AND ?
            )";
            $params[] = $priceMin;
            $params[] = $priceMax;
            $paramTypes .= "dd";
        }
    }

    // Sorting
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'name-asc':
                $query .= " ORDER BY pm.ProductName ASC";
                break;
            case 'name-desc':
                $query .= " ORDER BY pm.ProductName DESC";
                break;
            case 'date-new':
                $query .= " ORDER BY pm.ProductId DESC";
                break;
            case 'date-old':
                $query .= " ORDER BY pm.ProductId ASC";
                break;
            default:
                $query .= " ORDER BY pm.ProductId DESC";
                break;
        }
    } else {
        $query .= " ORDER BY pm.ProductId DESC";
    }

    // Execute query
    $FieldNames = array("ProductId", "ProductName", "PhotoPath", "ShortDescription", "CategoryId", "SubCategoryId", "IsCombo");
    $products = $obj->MysqliSelect1($query, $FieldNames, $paramTypes, $params);
    
    // Process products and get pricing information
    $processedProducts = array();

    if (!empty($products)) {
        foreach ($products as $product) {
            // Fetch price details for each product - simplified query
            $priceQuery = "SELECT OfferPrice, MRP, Size FROM product_price WHERE ProductId = ? ORDER BY OfferPrice ASC LIMIT 1";
            $product_prices = $obj->MysqliSelect1(
                $priceQuery,
                array("OfferPrice", "MRP", "Size"),
                "i",
                array($product["ProductId"])
            );

            // Set default values
            $lowest_price = 0;
            $mrp = 0;
            $savings = 0;
            $savings_percentage = 0;

            if (!empty($product_prices) && isset($product_prices[0])) {
                $price_data = $product_prices[0];
                $lowest_price = floatval($price_data["OfferPrice"]);
                $mrp = floatval($price_data["MRP"]);

                if ($mrp > $lowest_price && $lowest_price > 0) {
                    $savings = $mrp - $lowest_price;
                    $savings_percentage = round(($savings / $mrp) * 100);
                }
            }

            // Simple review count (default to 0 for now)
            $review_count = 0;

            $processedProducts[] = array(
                'ProductId' => $product['ProductId'],
                'ProductName' => $product['ProductName'],
                'PhotoPath' => $product['PhotoPath'],
                'ShortDescription' => $product['ShortDescription'],
                'lowest_price' => $lowest_price,
                'mrp' => $mrp,
                'savings' => $savings,
                'savings_percentage' => $savings_percentage,
                'review_count' => $review_count
            );
        }
    }
    
    // Return JSON response
    echo json_encode(array(
        'success' => true,
        'products' => $processedProducts,
        'total_count' => count($processedProducts),
        'debug' => array(
            'query' => $query,
            'params' => $params,
            'raw_products_count' => count($products ?? array()),
            'filters_received' => $filters
        )
    ));

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => array(
            'query' => isset($query) ? $query : 'Query not set',
            'params' => isset($params) ? $params : array(),
            'filters_received' => isset($filters) ? $filters : null
        )
    ));
}
?>
