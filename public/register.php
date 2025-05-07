<?php
require 'db.php';

// Get facility_id from URL (if coming from QR scan)
$facility_id = $_GET['facility_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $facility_id = $_POST['facility_id'] ?? null; // Preserve facility_id after form submission

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        // Redirect to login or directly to rating page if facility_id is set
        $redirect_url = $facility_id ? "rate_facility.php?facility_id=" . urlencode($facility_id) : "login.php?success=1";
        header("Location: $redirect_url");
        exit();
    } else {
        $error = "Failed to register. Try again!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Sign Up</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="" method="POST">
            <input type="hidden" name="facility_id" value="<?php echo htmlspecialchars($facility_id); ?>">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="auth-btn">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php?facility_id=<?php echo urlencode($facility_id); ?>">Login</a></p>
    </div>
</body>
</html>
