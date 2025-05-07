<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate all fields
$requiredFields = ['q1','q2','q3','q4','q5','q6','q7','q8','q9','q10', 'facility_id'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        die("<p style='color: red; text-align: center;'>Missing answer for $field.</p>");
    }
}

$facility_id = intval($_POST['facility_id']);

// Prepare statement
$sql = "INSERT INTO survey_responses (
    user_id, facility_id, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iissssssssss",
    $user_id,
    $facility_id,
    $_POST['q1'],
    $_POST['q2'],
    $_POST['q3'],
    $_POST['q4'],
    $_POST['q5'],
    $_POST['q6'],
    $_POST['q7'],
    $_POST['q8'],
    $_POST['q9'],
    $_POST['q10']
);

if ($stmt->execute()) {
    echo "<script>
        alert('Thank you for your feedback!');
        window.location.href = 'thank_you.php';
    </script>";
} else {
    echo "<p style='color: red; text-align: center;'>Failed to save your survey. Please try again.</p>";
}
?>
