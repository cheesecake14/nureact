<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

ini_set('display_errors', 1);
error_reporting(E_ALL);


// Get raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Retrieve credentials from request
$email = $data["email"] ?? "";
$password = $data["password"] ?? "";

// Database connection
$conn = new mysqli("localhost", "root", "", "facility_management");

if ($conn->connect_error) {
  echo json_encode(["success" => false, "message" => "Connection failed"]);
  exit();
}

// Use prepared statement to avoid SQL injection
$stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? AND password = ?");
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();
  echo json_encode(["success" => true, "user" => $user]);
} else {
  echo json_encode(["success" => false, "message" => "Invalid username or password"]);
}

$stmt->close();
$conn->close();
