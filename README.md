# 🚀 Campus Pulse - Event Management System

This is a full-stack event management system built with PHP, MySQL, CSS, and JavaScript. It provides a complete portal for two types of users: **Event Planners** (who can create and manage events) and **Attendees** (who can discover and register for events).



---

## ✨ Core Features

* **Dual User Roles:** Separate dashboards and permissions for 'Planners' and 'Users' (Attendees).
* **Secure Authentication:** Secure user registration and login with password hashing.
* **Full Event CRUD:** Planners can **C**reate, **R**ead, **U**pdate, and **D**elete their own events.
* **Image Uploads:** Planners can upload a unique image for each event.
* **Public Event Listings:** A homepage (`index.php`) that shows all upcoming events to the public.
* **Event Registration:** Attendees can register for events. The "Register" button is smart—it only appears for logged-in users who haven't registered yet.
* **Reporting:**
    * **For Planners:** View a list of all attendees registered for their event.
    * **For Users:** View a list of all events they are registered for.
* **Responsive Design:** Features a sticky, transparent header and a hamburger menu for a clean mobile experience.

---

## 🛠️ Tech Stack

* **Backend:** PHP
* **Database:** MySQL
* **Frontend:** HTML5, CSS3 (Flexbox, Grid), JavaScript (ES6+)
* **Development Environment:** XAMPP (Apache, MySQL)

---

## ⚙️ How to Run Locally

To get this project running on a local machine, follow these steps.

### 1. Prerequisites
* You must have **XAMPP** (or a similar PHP/MySQL environment) installed and running.

### 2. Get the Code
* Clone this repository into your `htdocs` folder (e.g., `C:/xampp/htdocs/event-system`).

### 3. Database Setup
1.  Open phpMyAdmin (`http://localhost/phpmyadmin`).
2.  Create a new database named **`event_db`**.
3.  Go to the "SQL" tab and run the queries below to create the `users`, `events`, and `registrations` tables.

    **`users` table:**
    ```sql
    CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(100) NOT NULL,
      `email` varchar(255) NOT NULL,
      `password` varchar(255) NOT NULL,
      `role` enum('user','planner') NOT NULL DEFAULT 'user',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `username` (`username`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ```

    **`events` table:**
    ```sql
    CREATE TABLE `events` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `event_date` datetime NOT NULL,
      `location` varchar(255) NOT NULL,
      `event_image` varchar(255) DEFAULT NULL,
      `planner_id` int(11) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `planner_id` (`planner_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ```

    **`registrations` table:**
    ```sql
    CREATE TABLE `registrations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `event_id` int(11) NOT NULL,
      `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `user_event_unique` (`user_id`,`event_id`),
      KEY `event_id` (`event_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ```

### 4. Create `db_connect.php`
* In the main project folder, create a file named `db_connect.php`.
* Paste this code inside it (it's set up for default XAMPP).

    ```php
    <?php
    $servername = "localhost";
    $username = "root";
    $password = ""; // Your XAMPP password (usually empty)
    $dbname = "event_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    ?>
    ```

### 5. Create `uploads/` Folder
* In the main project folder, create a new, empty folder named **`uploads`**. This is where event images will be stored.

### 6. You're Done!
* Open your browser and go to **`http://localhost/event-system`** (or whatever you named the project folder).