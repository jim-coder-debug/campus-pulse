<?php
session_start();
require_once 'db_connect.php'; // Include database connection

// 1. PHP Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] !== 'planner') {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';
$success_message = '';

// 2. PHP Form Processing Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $planner_id = $_SESSION['user_id'];
    $event_image_path = NULL;

    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $upload_dir = 'uploads/';
        $file = $_FILES['event_image'];
        $file_name = basename($file['name']);
        $unique_file_name = time() . '_' . $file_name;
        $target_file = $upload_dir . $unique_file_name;
        
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($image_type != "jpg" && $image_type != "png" && $image_type != "jpeg") {
            $error_message = "Sorry, only JPG, JPEG, & PNG files are allowed.";
        } else {
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $event_image_path = $target_file;
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        }
    }

    if (empty($error_message)) {
        $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, location, event_image, planner_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $title, $description, $event_date, $location, $event_image_path, $planner_id);

        if ($stmt->execute()) {
            $success_message = "Event created successfully!";
        } else {
            $error_message = "An error occurred: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Event - Campus Event System</title>
    
    <link rel="stylesheet" href="css/style.css?v=4">
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            if (menuToggle && mobileMenu) {
                menuToggle.addEventListener('click', function() {
                    mobileMenu.classList.toggle('is-active');
                });
            }
        });
    </script>
</head>
<body class="dashboard-page">

    <div class="header">
        <div class="header-logo"><h1>Campus Pulse</h1></div>
        
        <ul class="header-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                <?php if ($_SESSION['role'] == 'planner'): ?>
                    <li><a href="create_event.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'create_event.php') ? 'active' : ''; ?>">Create Event</a></li>
                    <li><a href="my_events.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'my_events.php') ? 'active' : ''; ?>">My Events</a></li>
                    <li><a href="planner_resources.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'planner_resources.php') ? 'active' : ''; ?>">Resources</a></li>
                <?php else: ?>
                    <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">All Events</a></li>
                    <li><a href="my_registrations.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'my_registrations.php') ? 'active' : ''; ?>">My Registrations</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">All Events</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="header-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" style="margin-right: 10px;">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
        
        <button class="menu-toggle" id="mobile-menu-toggle">☰</button>
    </div>
    <div class="mobile-menu" id="mobile-menu">
        <ul class="header-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                <?php if ($_SESSION['role'] == 'planner'): ?>
                    <li><a href="create_event.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'create_event.php') ? 'active' : ''; ?>">Create Event</a></li>
                    <li><a href="my_events.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'my_events.php') ? 'active' : ''; ?>">My Events</a></li>
                    <li><a href="planner_resources.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'planner_resources.php') ? 'active' : ''; ?>">Resources</a></li>
                <?php else: ?>
                    <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">All Events</a></li>
                    <li><a href="my_registrations.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'my_registrations.php') ? 'active' : ''; ?>">My Registrations</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">All Events</a></li>
            <?php endif; ?>
        </ul>
        <div class="header-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" style="margin-bottom: 10px;">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        
        <h2>Create a New Event</h2>
        
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
            
        <form id="createEventForm" action="create_event.php" method="POST" enctype="multipart/form-data" novalidate>
            
            <div id="form-error" class="error" style="display: none;"></div>
            
            <div class="form-group">
                <label for="title">Event Title:</label>
                <input type="text" id="title" name="title">
            </div>
            
            <div class="form-group">
                <label for="description">Event Description:</label>
                <textarea id="description" name="description" rows="5"></textarea>
            </div>
            
            <div class="form-group">
                <label for="event_date">Event Date and Time:</label>
                <input type="datetime-local" id="event_date" name="event_date">
            </div>

            <div class="form-group">
                <label for="location">Location / Venue:</label>
                <input type="text" id="location" name="location">
            </div>
            
            <div class="form-group">
                <label for="event_image">Event Image (Optional):</label>
                <input type="file" id="event_image" name="event_image" class="file-input">
            </div>
            
            <button type="submit" class="form-button">Create Event</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const eventForm = document.getElementById('createEventForm');
            const errorDiv = document.getElementById('form-error');
            if (eventForm) {
                eventForm.addEventListener('submit', function(event) {
                    // (Your validation code here)
                });
            }
        });
    </script>

</body>
</html>