<?php
session_start();
require_once 'db_connect.php';

$event_id = $_GET['id'] ?? null; // Get event ID from URL
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;

$event = null;
$error_message = '';
$success_message = '';
$is_registered = false;

if (!$event_id) {
    header("Location: index.php");
    exit();
}

// --- Handle Event Registration (if form is submitted) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_event'])) {
    if ($user_id && $user_role == 'user') {
        
        // Check if already registered (to prevent double-submissions)
        $stmt_check = $conn->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
        $stmt_check->bind_param("ii", $user_id, $event_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows == 0) {
            // Not registered, so insert
            $stmt_insert = $conn->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
            $stmt_insert->bind_param("ii", $user_id, $event_id);
            if ($stmt_insert->execute()) {
                $success_message = "You have been successfully registered for this event!";
                $is_registered = true;
            } else {
                $error_message = "An error occurred during registration. Please try again.";
            }
            $stmt_insert->close();
        } else {
            $success_message = "You are already registered for this event.";
            $is_registered = true;
        }
        $stmt_check->close();
        
    } else {
        $error_message = "You must be logged in as a user to register.";
    }
}
// --- End Registration Handling ---


// --- Fetch Event Details ---
// We JOIN with the users table to get the planner's name
$stmt = $conn->prepare("SELECT e.*, u.username AS planner_name 
                      FROM events e 
                      JOIN users u ON e.planner_id = u.id 
                      WHERE e.id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $event = $result->fetch_assoc();
} else {
    $error_message = "Event not found.";
}
$stmt->close();
// --- End Fetch Event Details ---

// --- Check if user is *already* registered (for displaying the button) ---
if ($user_id && $user_role == 'user' && !$is_registered) {
    $stmt_check = $conn->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
    $stmt_check->bind_param("ii", $user_id, $event_id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $is_registered = true;
    }
    $stmt_check->close();
}
// --- End Registration Check ---

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $event ? htmlspecialchars($event['title']) : 'Event Details'; ?> - Campus Pulse</title>
    <link rel="stylesheet" href="css/style.css?v=3">
</head>
<body class="dashboard-page">

    <div class="header">
        
        <div class="header-logo">
            <h1>Campus Pulse</h1>
        </div>
        
        <ul class="header-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                <?php if ($_SESSION['role'] == 'planner'): ?>
                    <li><a href="create_event.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'create_event.php') ? 'active' : ''; ?>">Create Event</a></li>
                    <li><a href="my_events.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'my_events.php') ? 'active' : ''; ?>">My Events</a></li>
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
        
        <button class="menu-toggle" id="mobile-menu-toggle">
            ☰
        </button>
        
    </div>
    <div class="mobile-menu" id="mobile-menu">
        <ul class="header-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                <?php if ($_SESSION['role'] == 'planner'): ?>
                    <li><a href="create_event.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'create_event.php') ? 'active' : ''; ?>">Create Event</a></li>
                    <li><a href="my_events.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'my_events.php') ? 'active' : ''; ?>">My Events</a></li>
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
        
        <?php if ($event): ?>
            <a href="index.php" class="back-link">&larr; Back to All Events</a>
            
            <div class="event-details-layout">
                
                <div class="event-details-image">
                    <?php if (!empty($event['event_image'])): ?>
                        <img src="<?php echo htmlspecialchars($event['event_image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                    <?php else: ?>
                        <div class="event-card-image-container">
                            <span>No Image Available</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="event-details-info">
                    <h2><?php echo htmlspecialchars($event['title']); ?></h2>
                    
                    <p class="event-detail-meta event-date">
                        <strong>When:</strong> <?php echo date('D, M j, Y \a\t g:i A', strtotime($event['event_date'])); ?>
                    </p>
                    <p class="event-detail-meta event-location">
                        <strong>Where:</strong> <?php echo htmlspecialchars($event['location']); ?>
                    </p>
                    <p class="event-detail-meta event-planner">
                        <strong>Event by:</strong> <?php echo htmlspecialchars($event['planner_name']); ?>
                    </p>
                    
                    <div class="registration-box">
                        <?php if (!empty($success_message)): ?>
                            <p class="success"><?php echo $success_message; ?></p>
                        <?php elseif (!empty($error_message)): ?>
                            <p class="error"><?php echo $error_message; ?></p>
                        <?php endif; ?>
                        
                        <?php if ($user_id): // User is logged in ?>
                            <?php if ($user_role == 'planner'): ?>
                                <p class="info-box">Event planners cannot register for events.</p>
                            <?php elseif ($is_registered): ?>
                                <p class="info-box registered">You are registered for this event!</p>
                            <?php else: // User is a 'user' and not registered ?>
                                <form action="view_event.php?id=<?php echo $event_id; ?>" method="POST">
                                    <button type="submit" name="register_event" class="form-button register-btn">
                                        Register for this Event
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                        <?php else: // User is a guest ?>
                            <p class="info-box">
                                <a href="login.php">Login</a> or <a href="register.php">Register</a> to attend this event.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div> <div class="event-full-description">
                <h3>About this Event</h3>
                <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            </div>
            
        <?php else: ?>
            <h2>Event Not Found</h2>
            <p class="error"><?php echo $error_message ? $error_message : "The event you are looking for does not exist."; ?></p>
            <a href="index.php" class="back-link">&larr; Back to All Events</a>
        <?php endif; ?>

    </div> <script>
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('is-active');
        });
    </script>
</body>
</html>