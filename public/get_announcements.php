<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "facility_management"; // Change to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT id, title, description, end_date FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);

$announcements = [];
while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}

echo json_encode($announcements);
$conn->close();
?>
