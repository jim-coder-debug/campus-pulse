<?php
session_start();
require_once 'db_connect.php';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error_message = "All fields are required.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            header("Location: login.php?message=Registration successful! Please log in.");
            exit();
        } else {
            if ($stmt->errno == 1062) {
                $error_message = "This username or email is already taken.";
            } else {
                $error_message = "An error occurred: " . $stmt->error;
            }
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
    <title>Register - Campus Event System</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>

    <div class="form-container">
        <form action="register.php" method="POST">
            <h2>Create Your Account</h2>

            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="role">I am a:</label>
                <select id="role" name="role" required>
                    <option value="">-- Select your role --</option>
                    <option value="user">Normal User (Attendee)</option>
                    <option value="planner">Event Planner</option>
                </select>
            </div>
            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
            <p class="back-to-home"><a href="index.php">View all events as guest</a></p>
        </form>
    </div>

</body>
</html>