<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
require 'db.php';

// Calculate overall average rating
$sql = "SELECT ROUND(AVG(rating), 1) AS overall_rating FROM reviews"; 
$result = $conn->query($sql);
$data = $result->fetch_assoc();

echo json_encode($data); // ✅ Returns { "overall_rating": 3.8 }
$conn->close();
?>
