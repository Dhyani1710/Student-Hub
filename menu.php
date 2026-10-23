<?php
$role = $_SESSION["role"] ?? "student";
?>

<nav class="studenthub-menu">

    <?php if ($role === "admin"): ?>

        <a href="AdminDashboard.php">Admin Dashboard</a>

        <a href="StudentManagement.php">Student Management</a>

        <a href="EventManagement.php">Event Management</a>

        <a href="EventAPI.php">Event API</a>

    <?php else: ?>

        <a href="Dashboard.php">Dashboard</a>

        <a href="Profile.php">Profile</a>

        <a href="Event.html">Events</a>

    <?php endif; ?>

    <a href="logout.php">Logout</a>

</nav>