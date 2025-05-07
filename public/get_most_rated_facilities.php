<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
require 'db.php';

$sql = "SELECT facilities.name, COUNT(reviews.id) AS total_ratings
        FROM facilities
        LEFT JOIN reviews ON facilities.facility_id = reviews.facility_id
        GROUP BY facilities.facility_id
        ORDER BY total_ratings DESC
        LIMIT 5";

$result = $conn->query($sql);
$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);
$conn->close();
?>
