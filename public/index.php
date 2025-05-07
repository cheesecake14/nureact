<?php
// index.php - API Placeholder

header('Content-Type: application/json');

echo json_encode([
    'status' => 'OK',
    'message' => 'Welcome to the API server.',
    'endpoints' => [
        '/login.php',
        '/register.php',
        '/get-user.php'
    ]
]);
