<?php
// Set headers first before any output
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
header("Access-Control-Allow-Headers: Content-Type");

// Buffer output to prevent any HTML error messages from being sent
ob_start();

// Include database connection
try {
    require_once 'db.php';
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $review_id = $_GET['review_id'] ?? null;

        if ($review_id) {
            $stmt = $conn->prepare("
                SELECT 
                    rep.id AS reply_id, 
                    rep.review_id, 
                    rep.email, 
                    rep.message, 
                    rep.sent_at,
                    rev.comment,
                    rev.created_at,
                    f.name AS facility_name
                FROM replies rep
                JOIN reviews rev ON rep.review_id = rev.id
                JOIN facilities f ON rev.facility_id = f.facility_id
                WHERE rep.review_id = ?
                ORDER BY rep.sent_at DESC
            ");
            $stmt->bind_param("i", $review_id);
        } else {
            $stmt = $conn->prepare("
                SELECT 
                    rep.id AS reply_id, 
                    rep.review_id, 
                    rep.email, 
                    rep.message, 
                    rep.sent_at,
                    rev.comment,
                    rev.created_at,
                    f.name AS facility_name
                FROM replies rep
                JOIN reviews rev ON rep.review_id = rev.id
                JOIN facilities f ON rev.facility_id = f.facility_id
                ORDER BY rep.sent_at DESC
            ");
        }

        if (!$stmt) {
            throw new Exception("SQL statement preparation failed: " . $conn->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $replies = [];
        while ($row = $result->fetch_assoc()) {
            $replies[] = $row;
        }

        echo json_encode(["success" => true, "replies" => $replies]);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);

        $review_id = $input['review_id'] ?? null;
        $email = $input['email'] ?? null;
        $message = $input['message'] ?? null;

        if (!$review_id || !$email || !$message) {
            throw new Exception("Missing required fields.");
        }

        $stmt = $conn->prepare("INSERT INTO replies (review_id, email, message, sent_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $review_id, $email, $message);

        if (!$stmt->execute()) {
            throw new Exception("Database insert failed: " . $stmt->error);
        }

        echo json_encode(["success" => true]);
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    // Clean the output buffer
    ob_clean();
    
    // Send proper JSON error response
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

// End the script
exit;
?>