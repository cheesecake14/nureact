<?php


$servername = "sql213.infinityfree.com";
$username = "if0_38923840";
$password = "EpLx5unbWe1p";
$dbname = "if0_38923840_facility_managementgit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
