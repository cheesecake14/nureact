<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include "db.php";

if (isset($_GET["review_id"])) {
    $review_id = intval($_GET["review_id"]);

    $sql = "SELECT id, review_id, email, message, sent_at FROM replies WHERE review_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $replies = [];
    while ($row = $result->fetch_assoc()) {
        $replies[] = $row;
    }

    echo json_encode($replies);
} else {
    echo json_encode(["error" => "Missing review_id"]);
}
?>
