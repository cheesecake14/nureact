<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include "db.php"; // Database connection

// Check if a specific facility is requested
if (isset($_GET["facility_id"])) {
    $facility_id = intval($_GET["facility_id"]);
    $sql = "
        SELECT r.id, 
               COALESCE(u.name, 'Anonymous') AS reviewer, 
               r.rating, 
               r.comment, 
               r.created_at 
        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.id
        WHERE r.facility_id = ?
        ORDER BY r.rating DESC, r.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $facility_id);
} else {
    $sql = "
        SELECT r.id, 
               COALESCE(u.name, 'Anonymous') AS reviewer, 
               r.rating, 
               r.comment, 
               r.created_at, 
               f.name AS facility_name
        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.id
        JOIN facilities f ON r.facility_id = f.facility_id
        ORDER BY r.rating DESC, r.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
}

// Execute query
$stmt->execute();
$result = $stmt->get_result();

$positive_reviews = [];
$negative_reviews = [];

while ($row = $result->fetch_assoc()) {
    $review = [
        "id" => (int) $row["id"],
        "reviewer" => $row["reviewer"], // Will be "Anonymous" if user_id is NULL
        "rating" => (int) $row["rating"],
        "comment" => $row["comment"],
        "date" => $row["created_at"],
        "facility" => isset($row["facility_name"]) ? $row["facility_name"] : null
    ];

    if ($row["rating"] >= 3) {
        $positive_reviews[] = $review;
    } else {
        $negative_reviews[] = $review;
    }
}

// Return JSON response
echo json_encode([
    "positive_reviews" => $positive_reviews,
    "negative_reviews" => $negative_reviews
], JSON_PRETTY_PRINT);

$conn->close();
?>
