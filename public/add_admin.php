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

// Validate input
if (!isset($data["name"], $data["email"], $data["password"], $data["role"])) {
    die(json_encode(["error" => "Missing required fields"]));
}

$name = $conn->real_escape_string($data["name"]);
$email = $conn->real_escape_string($data["email"]);
$password = password_hash($data["password"], PASSWORD_DEFAULT); // Secure password hashing
$role = $conn->real_escape_string($data["role"]);

$sql = "INSERT INTO admins (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "Admin added successfully"]);
} else {
    echo json_encode(["error" => "Error adding admin: " . $conn->error]);
}

$conn->close();
?>
