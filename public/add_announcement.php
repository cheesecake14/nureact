<?php
// Enable CORS
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

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["title"]) || !isset($data["description"]) || !isset($data["end_date"])) {
    die(json_encode(["error" => "Missing required fields"]));
}

$title = $conn->real_escape_string($data["title"]);
$description = $conn->real_escape_string($data["description"]);
$end_date = $conn->real_escape_string($data["end_date"]);

$sql = "INSERT INTO announcements (title, description, end_date) VALUES ('$title', '$description', '$end_date')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "Announcement added successfully"]);
} else {
    echo json_encode(["error" => "Error adding announcement: " . $conn->error]);
}

$conn->close();
?>
