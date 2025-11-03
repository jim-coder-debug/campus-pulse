<?php
session_start();
require_once 'db_connect.php';

// 1. PHP Security Check:
// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Redirect to dashboard if the user is NOT a 'planner'
if ($_SESSION['role'] !== 'planner') {
    header("Location: dashboard.php");
    exit();
}

$event_id = $_GET['id'] ?? null;
$planner_id = $_SESSION['user_id'];
$event_title = '';
$attendees = [];

if (!$event_id) {
    header("Location: my_events.php");
    exit();
}

// 2. Security Check: Verify this event belongs to this planner
$stmt = $conn->prepare("SELECT title, planner_id FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $event = $result->fetch_assoc();
    if ($event['planner_id'] != $planner_id) {
        // This event does not belong to this planner!
        header("Location: my_events.php");
        exit();
    }
    $event_title = $event['title'];
} else {
    // Event not found
    header("Location: my_events.php");
    exit();
}
$stmt->close();

// 3. Fetch all attendees for this event
// We JOIN with the users table to get their names and emails
$stmt_attendees = $conn->prepare("SELECT u.username, u.email 
                                FROM users u
                                JOIN registrations r ON u.id = r.user_id
                                WHERE r.event_id = ?");
$stmt_attendees->bind_param("i", $event_id);
$stmt_attendees->execute();
$result_attendees = $stmt_attendees->get_result();

if ($result_attendees->num_rows > 0) {
    while ($row = $result_attendees->fetch_assoc()) {
        $attendees[] = $row;
    }
}
$stmt_attendees->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendees - Campus Event System</title>
    
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
        
        <a href="my_events.php" class="back-link">&larr; Back to My Events</a>
        <h2>Attendees for "<?php echo htmlspecialchars($event_title); ?>"</h2>
        
        <?php if (empty($attendees)): ?>
            <p class="no-events" style="width: 100%;">
                No one has registered for this event yet.
            </p>
        <?php else: ?>
            <table class="attendee-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendees as $attendee): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attendee['username']); ?></td>
                            <td><?php echo htmlspecialchars($attendee['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
    </div> </body>
</html>