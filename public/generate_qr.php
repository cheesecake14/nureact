<?php
require 'phpqrcode/qrlib.php'; // Make sure you have the QR code library
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
$facility_id = $_GET['facility_id'] ?? null;

if (!$facility_id) {
    die("Invalid Facility ID.");
}

// Generate QR Code with a URL that leads to the rating page
$qr_text = "http://192.168.1.9/api/rate_facility.php?facility_id=" . $facility_id;
QRcode::png($qr_text);
?>
