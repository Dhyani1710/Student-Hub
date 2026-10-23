<?php
require_once "auth.php";
requireRole("admin");

require_once "db.php";

$message = "";
$messageType = "";

$fname = "";
$sname = "";
$username = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fname = trim($_POST["fname"] ?? "");
    $sname = trim($_POST["sname"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        $fname === "" ||
        $sname === "" ||
        $username === "" ||
        $email === "" ||
        $password === ""
    ) {
        $message = "All fields are required.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "error";

    } elseif (!preg_match("/^25DCS[A-Za-z0-9]+$/", $username)) {

        $message = "Username must start with 25DCS.";
        $messageType = "error";

    } elseif (strlen($password) < 8) {

        $message = "Password must contain at least 8 characters.";
        $messageType = "error";

    } else {

        $check = $conn->prepare(
            "SELECT student_id
             FROM students
             WHERE username = ? OR email = ?"
        );

        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "Username or email already exists.";
            $messageType = "error";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $role = "student";

            $stmt = $conn->prepare(
                "INSERT INTO students
                (fname, sname, username, email, password, role)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssss",
                $fname,
                $sname,
                $username,
                $email,
                $hashedPassword,
                $role
            );

            if ($stmt->execute()) {

                $message = "Student added successfully!";
                $messageType = "success";

                $fname = "";
                $sname = "";
                $username = "";
                $email = "";

            } else {

                $message = "Failed to add student.";
                $messageType = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student | StudentHub</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 500px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: #023e7d;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button, .btn {
            display: inline-block;
            background: #0466c8;
            color: white;
            border: none;
            padding: 12px 18px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
        }

        button:hover, .btn:hover {
            background: #023e7d;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Add New Student</h1>

    <?php if ($message !== ""): ?>
        <div class="<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="addStudent.php">

        <label>First Name</label>
        <input
            type="text"
            name="fname"
            value="<?php echo htmlspecialchars($fname, ENT_QUOTES, 'UTF-8'); ?>"
            required
        >

        <label>Surname</label>
        <input
            type="text"
            name="sname"
            value="<?php echo htmlspecialchars($sname, ENT_QUOTES, 'UTF-8'); ?>"
            required
        >

        <label>Username</label>
        <input
            type="text"
            name="username"
            placeholder="25DCS..."
            value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
            required
        >

        <label>Email</label>
        <input
            type="email"
            name="email"
            value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
            required
        >

        <label>Password</label>
        <input
            type="password"
            name="password"
            minlength="8"
            required
        >

        <br>

        <button type="submit">Add Student</button>

        <a href="StudentManagement.php" class="btn">
            Back
        </a>

    </form>

</div>

</body>
</html>

<?php
$conn->close();
?>