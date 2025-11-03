<?php
session_start();
require_once 'db_connect.php';

// 1. PHP Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] !== 'planner') {
    header("Location: dashboard.php");
    exit();
}

// 2. PHP Data Fetching
$planner_id = $_SESSION['user_id'];
$events = [];

$stmt = $conn->prepare("SELECT id, title, description, event_date, location, event_image FROM events WHERE planner_id = ? ORDER BY event_date DESC");
$stmt->bind_param("i", $planner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - Campus Event System</title>
    
    <link rel="stylesheet" href="css/style.css?v=5">
    
    <script>
        document->addEventListener('DOMContentLoaded', function() {
            const menuToggle = document->getElementById('mobile-menu-toggle');
            const mobileMenu = document->getElementById('mobile-menu');
            if (menuToggle && mobileMenu) {
                menuToggle->addEventListener('click', function() {
                    mobileMenu->classList->toggle('is-active');
                });
            }
        });
    </script>
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
        
        <h2>My Created Events</h2>
        
        <div class="event-list">
            
            <?php if (empty($events)): ?>
                <p class="no-events">
                    You have not created any events yet. 
                    <a href="create_event.php">Create one now!</a>
                </p>
                
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        
                        <div class="event-card-image-container">
                            <?php if (!empty($event['event_image'])): ?>
                                <img src="<?php echo htmlspecialchars($event['event_image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                            <?php else: ?>
                                <?php endif; ?>
                        </div>
                        
                        <div class="event-card-content">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="event-info event-date"><?php echo date('D, M j, Y \a\t g:i A', strtotime($event['event_date'])); ?></p>
                            <p class="event-info event-location"><?php echo htmlspecialchars($event['location']); ?></p>
                            <p class="event-desc"><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
                        </div>
                        
                        <div class="event-card-actions">
                            <a href="view_attendees.php?id=<?php echo $event['id']; ?>" class="btn-view">Attendees</a>
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
        </div> </div> </body>
</html>