<?php
session_start();
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planner Resources - Campus Event System</title>
    
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
        
        <h2>Planner Resources</h2>
        <p style="margin-bottom: 2rem; font-size: 1.1rem; color: #555;">A simple hub of free tools and guides to help you plan and market your events.</p>

        <div class="event-list">

            <div class="event-card">
                <div class="event-card-content">
                    <h3>Guides & Templates</h3>
                    <p class="event-desc" style="height: auto; margin-bottom: 1rem;">
                        Checklists, email templates, and planning guides to get you started.
                    </p>
                    <ul style="list-style-position: inside; padding-left: 10px; color: #333; line-height: 1.8;">
                        <li>Event Planning Checklist</li>
                        <li>Sample Marketing Email</li>
                        <li>Budget Tracking Template</li>
                    </ul>
                </div>
                <div class="event-card-actions">
                    <a href="#" class="btn-view" style="flex-grow: 0;">View Guides</a>
                </div>
            </div>

            <div class="event-card">
                <div class="event-card-content">
                    <h3>Free Tools</h3>
                    <p class="event-desc" style="height: auto; margin-bottom: 1rem;">
                        Links to free external tools for design and social media.
                    </p>
                     <ul style="list-style-position: inside; padding-left: 10px; color: #333; line-height: 1.8;">
                        <li>Canva (for posters)</li>
                        <li>Unsplash (for free photos)</li>
                        <li>Bitly (for short links)</li>
                    </ul>
                </div>
                <div class="event-card-actions">
                    <a href="#" class="btn-view" style="flex-grow: 0;">View Tools</a>
                </div>
            </div>

        </div> </div> </body>
</html>