<?php
include "db.php";
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
$data = json_decode(file_get_contents("php://input"));

$user_id = $data->user_id;
$facility_id = $data->facility_id;
$rating = $data->rating;
$comment = $data->comment;

$sql = "INSERT INTO reviews (user_id, facility_id, rating, comment) VALUES ($user_id, $facility_id, $rating, '$comment')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Review added successfully"]);
} else {
    echo json_encode(["error" => "Error: " . $conn->error]);
}
?>
