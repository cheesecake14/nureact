<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once "db.php"; // Include your database connection

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !isset($data->password)) {
    echo json_encode(["success" => false, "message" => "Missing email or password"]);
    exit;
}

$email = $data->email;
$password = $data->password;

// Check if email exists
$sql = "SELECT id, name, email, password, role FROM admins WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Try password_verify first (for hashed passwords)
    if (password_verify($password, $user['password']) || $password === $user['password']) {
        $token = bin2hex(random_bytes(32)); // Generate a simple token
        
        // Send response with role information
        echo json_encode([
            "success" => true,
            "token" => $token,
            "role" => $user['role']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Email not found"]);
}

$stmt->close();
$conn->close();
?>