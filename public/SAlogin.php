<?php
header("Access-Control-Allow-Origin: https://sparkly-croquembouche-f1782d.netlify.app");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 86400"); // Cache preflight response for 1 day

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

ini_set('display_errors', 1);
error_reporting(E_ALL);


// Get raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Retrieve credentials from request
$email = $data["email"] ?? "";
$password = $data["password"] ?? "";

// Database connection
$conn = new mysqli("sql213.infinityfree.com
", "if0_38923840", "EpLx5unbWe1p", "if0_38923840_facility_management");

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
