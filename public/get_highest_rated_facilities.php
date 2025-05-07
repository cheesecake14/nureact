<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

require 'db.php';

// Fetch the top 3 highest-rated facilities
$sql = "SELECT f.facility_id, f.name, ROUND(AVG(r.rating), 2) AS avg_rating, COUNT(r.rating) AS rating_count
        FROM facilities f
        JOIN reviews r ON f.facility_id = r.facility_id
        GROUP BY f.facility_id, f.name
        HAVING rating_count >= 3
        ORDER BY avg_rating DESC
        LIMIT 3";
 // Fetch the top 3 highest-rated facilities

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data, JSON_PRETTY_PRINT);
$conn->close();
?>
