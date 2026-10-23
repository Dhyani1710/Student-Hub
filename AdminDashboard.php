<?php

require_once "auth.php";
requireRole("admin");

require_once "db.php";
require_once "audit_log.php";

function safe($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Total students
$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM students
     WHERE role = 'student'"
);

$totalStudents = $result->fetch_assoc()["total"];

// Total admins
$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM students
     WHERE role = 'admin'"
);

$totalAdmins = $result->fetch_assoc()["total"];

// Total events
$result = $conn->query(
    "SELECT COUNT(*) AS total FROM events"
);

$totalEvents = $result->fetch_assoc()["total"];

// Total registrations
$result = $conn->query(
    "SELECT COUNT(*) AS total FROM registrations"
);

$totalRegistrations = $result->fetch_assoc()["total"];

// Latest registrations
$latestRegistrations = $conn->query(
    "SELECT *
     FROM registrations
     ORDER BY registration_id DESC
     LIMIT 5"
);

// Recent audit logs
$recentLogs = $conn->query(
    "SELECT *
     FROM audit_logs
     ORDER BY log_id DESC
     LIMIT 5"
);

// Admin session ID
$adminId = $_SESSION["student_id"] ?? 0;

// Record dashboard access
if ($adminId > 0) {
    addAuditLog(
        $conn,
        $adminId,
        "VIEW_DASHBOARD",
        "Admin opened the dashboard"
    );
}

$role = $_SESSION["role"] ?? "student";

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard | StudentHub</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f7fb;
}

.sidebar {
    width: 240px;
    background: #023e7d;
    color: white;
    position: fixed;
    top: 0;
    bottom: 0;
    padding: 25px 15px;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 35px;
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 13px;
    margin-bottom: 8px;
    border-radius: 6px;
}

.sidebar a:hover {
    background: #0466c8;
}

.main {
    margin-left: 240px;
    padding: 30px;
}

h1 {
    color: #023e7d;
}

.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 25px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.card h3 {
    color: #64748b;
    font-size: 16px;
}

.card h2 {
    color: #0466c8;
    font-size: 32px;
}

.section {
    background: white;
    margin-top: 30px;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th {
    background: #023e7d;
    color: white;
}

th, td {
    padding: 13px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

@media(max-width: 900px) {

    .cards {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media(max-width: 600px) {

    .sidebar {
        position: static;
        width: 100%;
    }

    .main {
        margin-left: 0;
    }

    .cards {
        grid-template-columns: 1fr;
    }

    .section {
        overflow-x: auto;
    }

}

</style>

</head>

<body>

<div class="sidebar">

    <h2>StudentHub</h2>

    <a href="AdminDashboard.php">
        Dashboard
    </a>

    <?php if ($role === "admin"): ?>

        <a href="StudentManagement.php">
            Student Management
        </a>

        <a href="EventManagement.php">
            Event Management
        </a>

        <a href="EventAPI.php">
            Event API
        </a>

    <?php endif; ?>

    <?php if ($role === "student"): ?>

        <a href="Profile.php">
            My Profile
        </a>

        <a href="Event.html">
            View Events
        </a>

    <?php endif; ?>

    <a href="logout.php">
        Logout
    </a>

</div>

<div class="main">

    <h1>Admin Dashboard</h1>

    <p>Welcome to StudentHub Administration</p>

    <div class="cards">

        <div class="card">
            <h3>Total Students</h3>
            <h2><?php echo safe($totalStudents); ?></h2>
        </div>

        <div class="card">
            <h3>Total Admins</h3>
            <h2><?php echo safe($totalAdmins); ?></h2>
        </div>

        <div class="card">
            <h3>Total Events</h3>
            <h2><?php echo safe($totalEvents); ?></h2>
        </div>

        <div class="card">
            <h3>Total Registrations</h3>
            <h2><?php echo safe($totalRegistrations); ?></h2>
        </div>

    </div>

    <div class="section">

        <h2>Latest Registrations</h2>

        <table>

            <tr>
                <th>Registration ID</th>
                <th>Student ID</th>
                <th>Event ID</th>
            </tr>

            <?php while ($row = $latestRegistrations->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo safe($row["registration_id"]); ?>
                    </td>

                    <td>
                        <?php echo safe($row["student_id"]); ?>
                    </td>

                    <td>
                        <?php echo safe($row["event_id"]); ?>
                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

    <div class="section">

        <h2>Recent Audit Logs</h2>

        <table>

            <tr>
                <th>Log ID</th>
                <th>Admin ID</th>
                <th>Action</th>
                <th>Details</th>
                <th>Date</th>
            </tr>

            <?php while ($row = $recentLogs->fetch_assoc()): ?>

                <tr>

                    <td><?php echo safe($row["log_id"]); ?></td>

                    <td><?php echo safe($row["admin_id"]); ?></td>

                    <td><?php echo safe($row["action"]); ?></td>

                    <td><?php echo safe($row["details"]); ?></td>

                    <td><?php echo safe($row["created_at"]); ?></td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>

</body>
</html>