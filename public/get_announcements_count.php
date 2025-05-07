<?php

require 'db.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
$sql = "SELECT COUNT(*) AS count FROM announcements";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

echo json_encode($data);
$conn->close();
?>
