<?php
require_once "auth.php";
requireRole("admin");

require_once "db.php";

if (empty($_SESSION["event_csrf_token"])) {
    $_SESSION["event_csrf_token"] = bin2hex(random_bytes(32));
}

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");

$message = $_SESSION["event_message"] ?? "";
$messageType = $_SESSION["event_message_type"] ?? "";

unset($_SESSION["event_message"], $_SESSION["event_message_type"]);

function safe($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$sql = "SELECT event_id, name, date, venue, category, poster
        FROM events
        WHERE (name LIKE ? OR venue LIKE ?)";

$searchValue = "%" . $search . "%";

if ($category !== "") {
    $sql .= " AND category = ?";
}

$sql .= " ORDER BY date DESC, event_id DESC";

$stmt = $conn->prepare($sql);

if ($category === "") {
    $stmt->bind_param("ss", $searchValue, $searchValue);
} else {
    $stmt->bind_param("sss", $searchValue, $searchValue, $category);
}

$stmt->execute();
$events = $stmt->get_result();

$categoryStmt = $conn->prepare(
    "SELECT DISTINCT category FROM events ORDER BY category"
);

$categoryStmt->execute();
$categories = $categoryStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management | StudentHub</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        .container {
            max-width: 1250px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        h1 {
            color: #023e7d;
            margin-top: 0;
        }

        .navigation {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .btn, button {
            display: inline-block;
            background: #0466c8;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover, button:hover {
            background: #023e7d;
        }

        .add {
            background: #16a34a;
        }

        .edit {
            background: #2563eb;
        }

        .delete {
            background: #dc2626;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        input, select {
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #023e7d;
            color: white;
            padding: 13px;
            text-align: center;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: center;
        }

        tr:hover {
            background: #f8fafc;
        }

        .poster {
            width: 85px;
            height: 85px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .actions form {
            margin: 0;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        @media (max-width: 700px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Event Management</h1>

    <div class="navigation">

        <a href="StudentManagement.php" class="btn">
            Student Management
        </a>

        <a href="EventManagement.php" class="btn">
            Event Management
        </a>

        <a href="logout.php" class="btn">
            Logout
        </a>

    </div>

    <?php if ($message !== ""): ?>
        <div class="<?php echo $messageType === "success" ? "success" : "error"; ?>">
            <?php echo safe($message); ?>
        </div>
    <?php endif; ?>

    <div class="toolbar">

        <form method="GET" class="search-form">

            <input
                type="text"
                name="search"
                placeholder="Search events..."
                value="<?php echo safe($search); ?>"
            >

            <select name="category">

                <option value="">All Categories</option>

                <?php while ($row = $categories->fetch_assoc()): ?>

                    <option
                        value="<?php echo safe($row["category"]); ?>"
                        <?php echo $category === $row["category"] ? "selected" : ""; ?>
                    >
                        <?php echo safe($row["category"]); ?>
                    </option>

                <?php endwhile; ?>

            </select>

            <button type="submit">Search</button>

            <a href="EventManagement.php" class="btn">
                Reset
            </a>

        </form>

        <a href="addEvent.php" class="btn add">
            + Add Event
        </a>

    </div>

    <div class="table-wrapper">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($events->num_rows > 0): ?>

                <?php while ($event = $events->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo (int)$event["event_id"]; ?>
                        </td>

                        <td>

                            <?php if (!empty($event["poster"])): ?>

                                <img
                                    src="uploads/events/<?php echo rawurlencode(basename($event["poster"])); ?>"
                                    alt="Event Poster"
                                    class="poster"
                                >

                            <?php else: ?>

                                No Poster

                            <?php endif; ?>

                        </td>

                        <td><?php echo safe($event["name"]); ?></td>

                        <td><?php echo safe($event["date"]); ?></td>

                        <td><?php echo safe($event["venue"]); ?></td>

                        <td><?php echo safe($event["category"]); ?></td>

                        <td>

                            <div class="actions">

                                <a
                                    href="editEvent.php?id=<?php echo (int)$event["event_id"]; ?>"
                                    class="btn edit"
                                >
                                    Edit
                                </a>

                                <form
                                    action="deleteEvent.php"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this event?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo (int)$event["event_id"]; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?php echo safe($_SESSION["event_csrf_token"]); ?>"
                                    >

                                    <button type="submit" class="delete">
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7">No events found.</td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>

<?php
$stmt->close();
$categoryStmt->close();
$conn->close();
?>