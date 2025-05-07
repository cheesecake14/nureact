<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require 'db.php';
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure user is logged in
if (!isset($_SESSION["user_id"]) && !isset($_POST["anonymous"])) {
    die(json_encode(["error" => "User not authenticated"]));
}

// Read input data
$data = $_POST;

if (!isset($data["facility_id"], $data["rating"], $data["comment"])) {
    die(json_encode(["error" => "Missing required fields"]));
}

// Handle anonymous rating
$user_id = isset($data["anonymous"]) ? NULL : $_SESSION["user_id"];
$facility_id = intval($data["facility_id"]);
$rating = intval($data["rating"]);
$comment = trim($data["comment"]);

// Insert into `reviews` table
$sql = "INSERT INTO reviews (user_id, facility_id, rating, comment) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "Query preparation failed: " . $conn->error]));
}

$stmt->bind_param("iiis", $user_id, $facility_id, $rating, $comment);

if ($stmt->execute()) {
    // ✅ If the review is not anonymous, send an email notification
    if ($user_id !== NULL) {
        // Retrieve user's email
        $emailQuery = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $emailQuery->bind_param("i", $user_id);
        $emailQuery->execute();
        $result = $emailQuery->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userEmail = $user["email"];

            // ✅ Send email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                // SMTP Configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Gmail SMTP Server
                $mail->SMTPAuth = true;
                $mail->Username = 'davejohnlavarias@gmail.com'; // 🔹 Replace with your Gmail
                $mail->Password = 'yzon izoy pqks dcrb'; // 🔹 Replace with your App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Email Headers
                $mail->setFrom('davejohnlavarias@gmail.com', 'NU R.E.A.C.T v2 Team');
                $mail->addAddress($userEmail);

                // Email Content
                $mail->isHTML(true);
                $mail->Subject = "Thank You for Your Feedback!";
                $mail->Body = "
                    <html>
                    <head>
                        <title>Thank You for Your Rating</title>
                    </head>
                    <body>
                        <p>Hi,</p>
                        <p>Thank you for rating our facility. Your feedback is valuable to us!</p>
                        <p><strong>Your Rating:</strong> {$rating} ⭐</p>
                        <p><strong>Your Comment:</strong> {$comment}</p>
                        <p>We appreciate your time and effort in helping us improve our services.</p>
                        <br>
                        <p>Best regards,</p>
                        <p>NU R.E.A.C.T v2 Team</p>
                    </body>
                    </html>
                ";

                // Send Email
                $mail->send();
            } catch (Exception $e) {
                die(json_encode(["error" => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"]));
            }
        }
    }

    echo json_encode(["success" => "Rating submitted successfully, email sent"]);
} else {
    echo json_encode(["error" => "Failed to submit rating"]);
}

// Close connection
$conn->close();
?>
