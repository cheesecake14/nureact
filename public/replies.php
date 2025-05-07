<?php
// replies.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include "db.php";

// Get JSON input and decode
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (
    isset($data["review_id"]) && is_numeric($data["review_id"]) &&
    isset($data["email"]) && !empty($data["email"]) &&
    isset($data["message"]) && !empty(trim($data["message"]))
) {
    $review_id = intval($data["review_id"]);
    $email = trim($data["email"]);
    $message = trim($data["message"]);

    // 1. Save reply to database
    $stmt = $conn->prepare("INSERT INTO replies (review_id, email, message, sent_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $review_id, $email, $message);
    $stmt->execute();
    $stmt->close();

    // 2. Send email
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'davejohnlavarias@gmail.com';
        $mail->Password = 'yzon izoy pqks dcrb'; // use App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('davejohnlavarias@gmail.com', 'NU Management ');
        $mail->addAddress($email);
        $mail->Subject = 'Reply to Your Review';
        $mail->Body = $message;

        $mail->send();

        echo json_encode(["success" => true, "message" => "Reply sent successfully."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Mailer Error: " . $mail->ErrorInfo]);
    }
} else {
    // Handle invalid or missing data
    echo json_encode([
        "success" => false,
        "message" => "Invalid data. Make sure review_id, email, and message are provided."
    ]);
}
?>
