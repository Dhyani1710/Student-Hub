<?php

session_start();

include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: StudentLogin.html");
    exit();
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    echo "<script>
        alert('Please enter Username and Password!');
        window.location.href='StudentLogin.html';
    </script>";
    exit();
}

$sql = "SELECT student_id, fname, sname, username, password, role
        FROM students
        WHERE username = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $username);

$stmt->execute();

$result = $stmt->get_result();

$student = $result->fetch_assoc();

if ($student && password_verify($password, $student["password"])) {

    session_regenerate_id(true);

    $_SESSION["student_id"] = $student["student_id"];
    $_SESSION["username"] = $student["username"];
    $_SESSION["fname"] = $student["fname"];
    $_SESSION["sname"] = $student["sname"];
    $_SESSION["role"] = $student["role"];
    $_SESSION["last_activity"] = time();

    $stmt->close();
    $conn->close();

    if ($student["role"] === "admin") {
        header("Location: StudentManagement.php");
    } else {
        header("Location: Dashboard.php");
    }

exit();

} else {

    $stmt->close();
    $conn->close();

    echo "<script>
        alert('Invalid Username or Password!');
        window.location.href='StudentLogin.html';
    </script>";

    exit();
}

?>