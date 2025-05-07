<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

require 'db.php'; // DB 

// GROQ API KEY
$groqApiKey = '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'; 

// Fetch survey_responses
$sql = "SELECT facility_id, GROUP_CONCAT(CONCAT_WS(',', q1, q2, q3, q4, q5, q6, q7, q8, q9, q10) SEPARATOR ';') AS responses
        FROM survey_responses
        GROUP BY facility_id";

$surveyResult = $conn->query($sql);

//query error
if (!$surveyResult) {
    echo json_encode(['error' => 'Database query failed', 'details' => $conn->error]);
    exit;
}

$responseArray = [];

while ($row = $surveyResult->fetch_assoc()) {
    $facilityId = (int)$row['facility_id'];
    $responses = $row['responses'];

    // Get the facility name 
    $facilityStmt = $conn->prepare("SELECT name FROM facilities WHERE facility_id = ?");
    $facilityStmt->bind_param("i", $facilityId);
    $facilityStmt->execute();
    $facilityResult = $facilityStmt->get_result();
    $facilityData = $facilityResult->fetch_assoc();
    $facilityName = $facilityData ? $facilityData['name'] : 'Unknown Facility';

    $prompt = "Here are the survey responses (rated 1 to 5) for $facilityName:\n\n$responses\n\nBased on these results, provide a short and clear suggestion to improve the service quality at this facility.";

    // Groq API payload
    $payload = json_encode([
        "model" => "llama3-8b-8192",
        "messages" => [
            ["role" => "system", "content" => "You are a helpful assistant that gives facility improvement suggestions based on survey feedback."],
            ["role" => "user", "content" => $prompt]
        ]
    ]);

    // Send request to Groq API
    $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $groqApiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $apiResponse = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpStatus !== 200 || curl_errno($ch)) {
        $responseArray[] = [
            'facility' => $facilityName,
            'suggestion' => "Error from Groq API: " . curl_error($ch)
        ];
    } else {
        $data = json_decode($apiResponse, true);
        $suggestion = $data['choices'][0]['message']['content'] ?? 'No suggestion returned.';
        $responseArray[] = [
            'facility' => $facilityName,
            'suggestion' => trim($suggestion)
        ];
    }

    curl_close($ch);
}

echo json_encode($responseArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$conn->close();
?>
