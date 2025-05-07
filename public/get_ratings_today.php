<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require 'db.php';

$sql = "SELECT 
          SUM(CASE WHEN HOUR(created_at) BETWEEN 5 AND 11 THEN 1 ELSE 0 END) AS morning,
          SUM(CASE WHEN HOUR(created_at) BETWEEN 12 AND 17 THEN 1 ELSE 0 END) AS afternoon,
          SUM(CASE WHEN HOUR(created_at) BETWEEN 18 AND 23 THEN 1 ELSE 0 END) AS evening
        FROM reviews
        WHERE DATE(created_at) = CURDATE()";

$result = $conn->query($sql);
$data = $result->fetch_assoc();

$pieData = [
    ["name" => "Morning", "value" => (int)$data["morning"], "color" => "#4cc9f0"],
    ["name" => "Afternoon", "value" => (int)$data["afternoon"], "color" => "#4361ee"],
    ["name" => "Evening", "value" => (int)$data["evening"], "color" => "#4895ef"],
];

echo json_encode($pieData);
$conn->close();
?>
