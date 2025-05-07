<?php
header("Content-Type: application/json");

$host = 'localhost';  // your database host
$dbname = 'facility_management';  // your database name
$username = 'root';  // your database username
$password = '';  // your database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    exit;
}

// Get review_id from the query string
$review_id = isset($_GET['id']) ? $_GET['id'] : null;

// Validate that review_id is provided
if ($review_id) {
    // Prepare the SQL query to fetch review data by review_id
    $sql = "SELECT id, user_id, facility_id, rating, comment, created_at FROM reviews WHERE id = :review_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the review data
    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($review) {
        // Return the review data as JSON
        echo json_encode($review);
    } else {
        echo json_encode(["error" => "Review not found"]);
    }
} else {
    echo json_encode(["error" => "Review ID is required"]);
}
?>
