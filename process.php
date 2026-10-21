<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fname = trim($_POST["fname"] ?? "");
    $sname = trim($_POST["sname"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";

    if (
        $fname == "" ||
        $sname == "" ||
        $username == "" ||
        $password == ""
    ) {
        die("All fields are required.");
    }

    if (!preg_match("/^[A-Z]/", $fname) ||
        !preg_match("/^[A-Z]/", $sname)) {
        die("First name and surname must start with a capital letter.");
    }

    if (strpos($username, "25DCS") !== 0) {
        die("Username must start with 25DCS.");
    }

    if (strlen($password) < 8) {
        die("Password must contain at least 8 characters.");
    }

    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }

    if (!isset($_POST["agree"])) {
        die("Please accept Terms and Conditions.");
    }

    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $sql = "INSERT INTO students
            (fname, sname, username, password)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssss",
        $fname,
        $sname,
        $username,
        $hashedPassword
    );

    try {

        $stmt->execute();

        echo "<script>
            alert('Account Created Successfully!');
            window.location.href='StudentLogin.html';
        </script>";

    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() == 1062) {
            echo "Username already exists.";
        } else {
            echo "Unable to create account.";
        }
    }

    $stmt->close();
}

$conn->close();

?>