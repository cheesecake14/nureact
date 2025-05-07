<?php
// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facility_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["name"]) && isset($data["location"])) {
    $name = $conn->real_escape_string($data["name"]);
    $location = $conn->real_escape_string($data["location"]);

    $sql = "INSERT INTO facilities (name, location) VALUES ('$name', '$location')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Facility added successfully"]);
    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Invalid input data"]);
}

$conn->close();
?>
