<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include "db.php";

function showMessage($message, $redirect = "Register.html") {
    echo "<script>";
    echo "alert(" . json_encode(
        $message,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) . ");";
    echo "window.location.href=" . json_encode($redirect) . ";";
    echo "</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    showMessage("Invalid request.");
}

$fname = trim($_POST["fname"] ?? "");
$sname = trim($_POST["sname"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

if (
    $fname === "" ||
    $sname === "" ||
    $username === "" ||
    $email === "" ||
    $password === "" ||
    $confirmPassword === ""
) {
    showMessage("All fields are required.");
}

if (
    !preg_match("/^[A-Z]/", $fname) ||
    !preg_match("/^[A-Z]/", $sname)
) {
    showMessage("First Name and Surname must start with a capital letter.");
}

if (
    strlen($fname) > 50 ||
    strlen($sname) > 50
) {
    showMessage("First Name and Surname must not exceed 50 characters.");
}

if (
    !preg_match("/^25DCS[A-Za-z0-9]*$/", $username) ||
    strlen($username) > 50
) {
    showMessage("Username must start with 25DCS and contain only letters and numbers.");
}

if (
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 100
) {
    showMessage("Please enter a valid Email Address.");
}

if (strlen($password) < 8) {
    showMessage("Password must be at least 8 characters.");
}

if ($password !== $confirmPassword) {
    showMessage("Password and Confirm Password must be same.");
}

if (!isset($_POST["agree"]) || $_POST["agree"] !== "yes") {
    showMessage("Please accept Terms and Conditions.");
}

try {

    // Check duplicate username
    $checkUsername = $conn->prepare(
        "SELECT student_id FROM students WHERE username = ?"
    );

    $checkUsername->bind_param("s", $username);
    $checkUsername->execute();
    $checkUsername->store_result();

    if ($checkUsername->num_rows > 0) {
        showMessage("Username already exists. Please use another username.");
    }

    $checkUsername->close();

    // Check duplicate email
    $checkEmail = $conn->prepare(
        "SELECT student_id FROM students WHERE email = ?"
    );

    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        showMessage("Email already registered. Please use another email.");
    }

    $checkEmail->close();

    // Hash password
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // Insert student into database
    $sql = "INSERT INTO students
            (fname, sname, username, email, password)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssss",
        $fname,
        $sname,
        $username,
        $email,
        $hashedPassword
    );

    $stmt->execute();
    $stmt->close();

    $conn->close();

    showMessage(
        "Account Created Successfully!",
        "StudentLogin.html"
    );

} catch (mysqli_sql_exception $e) {

    if ($e->getCode() == 1062) {
        showMessage("Username or Email already exists.");
    }

    error_log("StudentHub Registration Error: " . $e->getMessage());

    showMessage("Unable to create account. Please try again.");
}

?>