<?php
include "db.php";
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
$review_id = $_GET['id'];

$sql = "DELETE FROM reviews WHERE id = $review_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Review deleted successfully"]);
} else {
    echo json_encode(["error" => "Error deleting review"]);
}
?>
