<?php
// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facility_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch facilities
$sql = "SELECT facility_id, name, location FROM facilities";
$result = $conn->query($sql);

$facilities = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $facilities[] = $row;
    }
}

echo json_encode($facilities);
$conn->close();
?>
