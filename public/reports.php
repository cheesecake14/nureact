<?php
session_start();
require 'db.php';

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$facility_id = $_GET["facility_id"] ?? null;

// Check if facility ID is valid
if (!$facility_id) {
    die("<p style='color: red; text-align: center;'>Invalid Facility ID.</p>");
}

// Fetch facility details
$sql = "SELECT name FROM facilities WHERE facility_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $facility_id);
$stmt->execute();
$result = $stmt->get_result();
$facility = $result->fetch_assoc();

if (!$facility) {
    die("<p style='color: red; text-align: center;'>Facility not found.</p>");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $report = trim($_POST["report"]);

    if (empty($report)) {
        $error = "Report cannot be empty!";
    } else {
        $sql = "INSERT INTO reports (user_id, facility_id, report) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $user_id, $facility_id, $report);

        if ($stmt->execute()) {
            header("Location: reports.php?facility_id=$facility_id&success=1");
            exit();
        } else {
            $error = "Failed to submit report. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Facility Issue - <?php echo htmlspecialchars($facility['name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color:  #32418C;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .report-container {
            width: 90%;
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: center;
        }
        h1 { color: #32418C; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }
        .submit-btn, .back-btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: bold;
        }
        .submit-btn {
            background: #32418C;
            color: white;
        }
        .submit-btn:hover { background: #283373; }
        .back-btn {
            background: #FBD117;
            color: black;
            text-decoration: none;
            display: block;
            text-align: center;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .back-btn:hover { background: #e0b800; }
        .success-message {
            color: green;
            font-weight: bold;
        }
        .error-message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h1>Report Issue - <?php echo htmlspecialchars($facility['name']); ?></h1>

        <?php if (isset($_GET["success"])): ?>
            <p class="success-message">Report submitted successfully!</p>
        <?php elseif (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="report">Describe the issue:</label>
            <textarea name="report" id="report" placeholder="Example: The aircon is not working..." required></textarea>

            <button type="submit" class="submit-btn">Submit Report</button>
        </form>

        <a href="rate_facility.php?facility_id=<?php echo $facility_id; ?>" class="back-btn">Go Back</a>
    </div>
</body>
</html>
