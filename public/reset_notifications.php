<?php

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ensure this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

// Database connection settings
include 'db.php';

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

try {
    // Update the reports to mark them as read
    // Assuming you have a 'is_new' or similar column in your reports table
    $stmt = $conn->prepare("UPDATE reports SET is_new = 0 WHERE is_new = 1");
    
    if ($stmt->execute()) {
        // Get number of affected rows
        $affectedRows = $stmt->affected_rows;
        
        echo json_encode([
            "success" => true, 
            "message" => "Notifications reset successfully",
            "reset_count" => $affectedRows
        ]);
    } else {
        throw new Exception("Failed to reset notifications");
    }
    
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
    // Close the database connection
    $conn->close();
}
?>