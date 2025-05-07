<?php
require 'db.php'; // Database connection

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'], $data['title'], $data['description'], $data['end_date'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit();
}

$id = $data['id'];
$title = $data['title'];
$description = $data['description'];
$end_date = $data['end_date'];

// Update announcement in the database
$sql = "UPDATE announcements SET title = ?, description = ?, end_date = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $title, $description, $end_date, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Announcement updated successfully."]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to update announcement."]);
}

$stmt->close();
$conn->close();
?>
