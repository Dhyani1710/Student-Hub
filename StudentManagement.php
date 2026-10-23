<?php

require_once "auth.php";
requireRole("admin");

require_once "db.php";

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = $_SESSION["student_message"] ?? "";
$messageType = $_SESSION["student_message_type"] ?? "";

unset(
    $_SESSION["student_message"],
    $_SESSION["student_message_type"]
);

$search = trim($_GET["search"] ?? "");
$filter = $_GET["filter"] ?? "all";

if (!in_array($filter, ["all", "student", "admin"], true)) {
    $filter = "all";
}

$sql = "SELECT student_id, fname, sname, username, email, role
        FROM students
        WHERE (
            fname LIKE ?
            OR sname LIKE ?
            OR username LIKE ?
            OR email LIKE ?
        )";

$searchValue = "%" . $search . "%";

if ($filter !== "all") {

    $sql .= " AND role = ?";
}

$sql .= " ORDER BY student_id DESC";

$stmt = $conn->prepare($sql);

if ($filter === "all") {

    $stmt->bind_param(
        "ssss",
        $searchValue,
        $searchValue,
        $searchValue,
        $searchValue
    );

} else {

    $stmt->bind_param(
        "sssss",
        $searchValue,
        $searchValue,
        $searchValue,
        $searchValue,
        $filter
    );
}

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Management | StudentHub</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: #023e7d;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button, .btn {
            background: #0466c8;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        button:hover, .btn:hover {
            background: #023e7d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #023e7d;
            color: white;
            padding: 12px;
        }

        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #f1f5f9;
        }

        .edit {
            background: #16a34a;
        }

        .delete {
            background: #dc2626;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .table-wrapper {
            overflow-x: auto;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Student Management</h1>
    <?php if ($message !== ""): ?>

    <div style="
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 6px;
        background: <?php echo $messageType === "success" ? "#dcfce7" : "#fee2e2"; ?>;
        color: <?php echo $messageType === "success" ? "#166534" : "#991b1b"; ?>;
    ">
        <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
    </div>

<?php endif; ?>
        <div style="margin-bottom: 20px;">

        <a href="StudentManagement.php" class="btn">
            Student Management
        </a>

        <a href="EventManagement.php" class="btn">
            Event Management
        </a>

        <a href="EventAPI.html" class="btn">
            Event API Management
        </a>

        <a href="logout.php" class="btn">
            Logout
        </a>

    </div>

    <div class="top-bar">

        <form method="GET">

            <input
                type="text"
                name="search"
                placeholder="Search students..."
                value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
            >

            <select name="filter">

                <option value="all" <?php if ($filter === "all") echo "selected"; ?>>
                    All Roles
                </option>

                <option value="student" <?php if ($filter === "student") echo "selected"; ?>>
                    Students
                </option>

                <option value="admin" <?php if ($filter === "admin") echo "selected"; ?>>
                    Admins
                </option>

            </select>

            <button type="submit">Search</button>

            <a href="StudentManagement.php" class="btn">
                Reset
            </a>

        </form>

        <a href="addStudent.php" class="btn">
            + Add Student
        </a>

    </div>

    <div class="table-wrapper">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Surname</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result->num_rows > 0): ?>

                <?php while ($student = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo (int)$student["student_id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($student["fname"], ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($student["sname"], ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($student["username"], ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($student["email"], ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($student["role"], ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>

                            <div class="actions">

                                <a
                                    href="editStudent.php?id=<?php echo (int)$student["student_id"]; ?>"
                                    class="btn edit"
                                >
                                    Edit
                                </a>

                                <?php if ($student["role"] === "student"): ?>

<form
    method="POST"
    action="deleteStudent.php"
    onsubmit="return confirm('Are you sure you want to delete this student?');"
    style="margin: 0;"
>

    <input
        type="hidden"
        name="id"
        value="<?php echo (int)$student["student_id"]; ?>"
    >

    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8"); ?>"
    >

    <button type="submit" class="btn delete">
        Delete
    </button>

</form>

<?php endif; ?>
                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7">
                        No students found.
                    </td>
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
$conn->close();

?>