<?php
// Set content type to JSON
header("Content-Type: application/json");

// Include database configuration
include '../database/config.php';

// Get the search query
$q = isset($_GET['s']) ? trim($_GET['s']) : '';

if (!empty($q)) {
    // Prepare the search term for LIKE query
    $searchTerm = "%" . $q . "%";

    // Prepare the SQL query with placeholders
    $sql = "SELECT ProductName, PhotoPath, ProductId FROM product_master 
            WHERE ProductName LIKE ? 
            OR ShortDescription LIKE ? 
            OR Specification LIKE ? 
            OR MetaTags LIKE ? 
            OR MetaKeywords LIKE ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters (all string types)
        $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        
        // Execute the query
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();

        // Initialize an array to hold the results
        $resultsArray = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Add each row to the results array
                $resultsArray[] = array(
                    'ProductName' => htmlspecialchars($row['ProductName']),
                    'PhotoPath' => htmlspecialchars($row['PhotoPath'])
                );
            }
        } else {
            // If no results found, return a message
            $resultsArray[] = array('message' => 'No results found.');
        }

        // Output the JSON encoded results
        echo json_encode($resultsArray);

        // Close the statement
        $stmt->close();
    } else {
        // If there's an error preparing the statement, return an error message
        echo json_encode(array('error' => 'Database error: Unable to prepare statement.'));
    }
} else {
    // If no search term is provided, return a message
    echo json_encode(array('message' => 'Please enter a search term.'));
}

// Close the database connection
$conn->close();
?>