<?php
require_once "auth.php";
requireRole("admin");

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$token = $_POST["csrf_token"] ?? "";

if (
    !$id ||
    !isset($_SESSION["csrf_token"]) ||
    !is_string($token) ||
    !hash_equals($_SESSION["csrf_token"], $token)
) {
    http_response_code(400);
    exit("Invalid deletion request.");
}

$stmt = $conn->prepare(
    "DELETE FROM students
     WHERE student_id = ? AND role = 'student'"
);

$stmt->bind_param("i", $id);

try {
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION["student_message"] = "Student deleted successfully!";
        $_SESSION["student_message_type"] = "success";
    } else {
        $_SESSION["student_message"] = "Student not found or cannot be deleted.";
        $_SESSION["student_message_type"] = "error";
    }

} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    $_SESSION["student_message"] =
        "Failed to delete student. The record may be used elsewhere.";

    $_SESSION["student_message_type"] = "error";
}

$stmt->close();
$conn->close();

header("Location: StudentManagement.php");
exit();
?>