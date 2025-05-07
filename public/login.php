<?php
session_start();
require 'db.php';

// Get facility_id from URL (if coming from QR scan)
$facility_id = $_GET['facility_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $facility_id = $_POST['facility_id'] ?? null; // Preserve facility_id from form submission

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // Redirect to rating page with facility_id if available
        $redirect_url = $facility_id ? "rate_facility.php?facility_id=" . urlencode($facility_id) : "rate_facility.php";
        header("Location: $redirect_url");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Login</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form action="" method="POST">
            <input type="hidden" name="facility_id" value="<?php echo htmlspecialchars($facility_id); ?>">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="auth-btn">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php?facility_id=<?php echo urlencode($facility_id); ?>">Sign up</a></p>
    </div>
</body>
</html>
