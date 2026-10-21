<?php

session_start();

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $sql = "SELECT student_id, fname, username, password
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

        header("Location: Dashboard.html");

        exit();

    } else {

        echo "<script>
                alert('Invalid Username or Password!');
                window.location.href='StudentLogin.html';
              </script>";
    }

    $stmt->close();
}

$conn->close();

?>