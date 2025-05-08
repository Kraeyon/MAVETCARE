<?php
// Include database connection
require_once "config/db_connect.php";

// Check if client_code is provided
if (isset($_GET['client_code']) && !empty($_GET['client_code'])) {
    $client_code = pg_escape_string($conn, $_GET['client_code']);
    
    // Query to fetch pets for the selected client
    $query = "SELECT pet_code, pet_name, pet_breed, pet_type FROM pet WHERE client_code = '$client_code' ORDER BY pet_name";
    $result = pg_query($conn, $query);
    
    // Check if query was successful
    if (!$result) {
        echo '<option value="">Error loading pets</option>';
        exit;
    }
    
    // Check if any pets exist for this client
    if (pg_num_rows($result) > 0) {
        echo '<option value="">Select Pet</option>';
        
        // Loop through the results and create options
        while ($pet = pg_fetch_assoc($result)) {
            echo '<option value="' . $pet['pet_code'] . '">' . 
                    $pet['pet_name'] . ' (' . $pet['pet_breed'] . ' ' . $pet['pet_type'] . ')</option>';
        }
    } else {
        echo '<option value="">No pets found for this client</option>';
    }
    
    // Free result set
    pg_free_result($result);
} else {
    echo '<option value="">Select Client First</option>';
}

// Close database connection
pg_close($conn);
?>