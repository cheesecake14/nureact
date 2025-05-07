<?php
require 'db.php'; // Database connection

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(["success" => false, "error" => "Missing announcement ID."]);
    exit();
}

$id = $data['id'];

// Delete announcement from the database
$sql = "DELETE FROM announcements WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Announcement deleted successfully."]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to delete announcement."]);
}

$stmt->close();
$conn->close();
?>
