<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include "db.php"; // Database connection

$reviews = [];

if (isset($_GET["facility_id"])) {
    $facility_id = intval($_GET["facility_id"]);

    $sql = "
        SELECT 
            r.id,
            CASE 
                WHEN u.name IS NOT NULL AND u.name != '' THEN u.name
                ELSE LEFT(SHA1(u.email), 10)
            END AS reviewer,
            u.email,
            r.rating,
            r.comment,
            r.created_at 
        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.id
        WHERE r.facility_id = $facility_id
    ";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $reviews[] = [
            "id" => (int) $row["id"],
            "reviewer" => $row["reviewer"],
            "email" => $row["email"], // ✅ Email for reply feature
            "rating" => (int) $row["rating"],
            "comment" => $row["comment"],
            "date" => $row["created_at"]
        ];
    }
} else {
    $sql = "
        SELECT 
            f.facility_id, 
            f.name, 
            COALESCE(AVG(r.rating), 0) AS overall_rating, 
            COALESCE(GROUP_CONCAT(DISTINCT 
                CASE 
                    WHEN u.name IS NOT NULL AND u.name != '' THEN u.name
                    ELSE LEFT(SHA1(u.email), 10)
                END SEPARATOR ', '
            ), '') AS reviewers,
            COALESCE(GROUP_CONCAT(DISTINCT r.comment SEPARATOR '|'), '') AS comments
        FROM facilities f
        LEFT JOIN reviews r ON f.facility_id = r.facility_id
        LEFT JOIN users u ON r.user_id = u.id
        GROUP BY f.facility_id, f.name
    ";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $reviews[] = [
            "facility_id" => (int) $row["facility_id"],
            "name" => $row["name"],
            "overallRating" => round($row["overall_rating"], 1),
            "reviewers" => !empty($row["reviewers"]) ? explode(", ", $row["reviewers"]) : [],
            "comments" => !empty($row["comments"]) ? explode("|", $row["comments"]) : []
        ];
    }
}

echo json_encode($reviews);
$conn->close();
?>
