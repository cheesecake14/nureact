<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require "db.php";

// Fetch all reports
$sql = "
    SELECT r.id, u.name AS reporter, f.name AS facility, r.report, r.created_at 
    FROM reports r
    JOIN users u ON r.user_id = u.id
    JOIN facilities f ON r.facility_id = f.facility_id
    ORDER BY r.created_at DESC
";
$result = $conn->query($sql);

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = [
        "id" => (int) $row["id"],
        "reporter" => $row["reporter"],
        "facility" => $row["facility"],
        "report" => $row["report"],
        "date" => $row["created_at"]
    ];
}

// Count unread reports (Assuming new reports are those created today)
$sql = "SELECT COUNT(*) AS new_reports FROM reports WHERE DATE(created_at) = CURDATE()";
$newReportsResult = $conn->query($sql);
$newReportCount = $newReportsResult->fetch_assoc()["new_reports"] ?? 0;

// Return JSON response
echo json_encode(["reports" => $reports, "new_reports" => $newReportCount]);
$conn->close();
?>
