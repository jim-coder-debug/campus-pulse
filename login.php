<?php
session_start();
require_once 'db_connect.php';

$error_message = '';
$message = $_GET['message'] ?? ''; // Get success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Campus Event System</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>

    <div class="form-container">
        <form action="login.php" method="POST">
            <h2>Login to Your Account</h2>

            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <p class="success"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p class="back-to-home"><a href="index.php">View all events as guest</a></p>
        </form>
    </div>

</body>
</html>