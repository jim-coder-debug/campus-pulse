<?php
session_start();
require_once 'db_connect.php';

// 1. Security Check: Must be a logged-in planner
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'planner') {
    // If not a planner or not logged in, just go to login
    header("Location: login.php");
    exit();
}

// 2. Check if an event ID was provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // No ID provided, go back to the events page
    header("Location: my_events.php");
    exit();
}

$event_id = $_GET['id'];
$planner_id = $_SESSION['user_id']; // The ID of the logged-in planner

// 3. Get event details (image path and planner_id) BEFORE deleting
$stmt = $conn->prepare("SELECT event_image, planner_id FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $event = $result->fetch_assoc();
    
    // 4. CRITICAL Security Check: Does this event belong to this planner?
    if ($event['planner_id'] != $planner_id) {
        // This is not their event! Redirect them.
        header("Location: my_events.php");
        exit();
    }

    // 5. Delete the physical image file (if it exists)
    if (!empty($event['event_image']) && file_exists($event['event_image'])) {
        unlink($event['event_image']); // Deletes the file
    }

    // 6. Delete associated registrations (from 'registrations' table)
    // This is good practice so you don't have orphan data.
    $stmt_reg = $conn->prepare("DELETE FROM registrations WHERE event_id = ?");
    $stmt_reg->bind_param("i", $event_id);
    $stmt_reg->execute();
    $stmt_reg->close();

    // 7. Delete the event from the 'events' table
    $stmt_event = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt_event->bind_param("i", $event_id);
    $stmt_event->execute();
    $stmt_event->close();

}
$stmt->close();
$conn->close();

// 8. Redirect back to the "My Events" page
header("Location: my_events.php");
exit();
?>