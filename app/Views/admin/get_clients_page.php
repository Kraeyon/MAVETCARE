<?php
// Include database connection
require_once "config/db_connect.php";

// Check if client_code is provided
if (isset($_GET['client_code'])) {
    $client_code = mysqli_real_escape_string($conn, $_GET['client_code']);
    
    // Get pets for the client
    $query = "SELECT pet_code, pet_name, pet_type, pet_breed, pet_age 
                FROM pet 
                WHERE client_code = '$client_code' 
                ORDER BY pet_name";
                
    $result = mysqli_query($conn, $query);
    
    // Check if any pets found
    if (mysqli_num_rows($result) > 0) {
        echo '<option value="">Select Pet</option>';
        
        while ($pet = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $pet['pet_code'] . '">' . 
                    $pet['pet_name'] . ' (' . $pet['pet_breed'] . ', ' . $pet['pet_age'] . ' yrs)' . 
                    '</option>';
        }
    } else {
        echo '<option value="">No pets found for this client</option>';
    }
} else {
    echo '<option value="">Select client first</option>';
}

// Close the database connection
mysqli_close($conn);
?>