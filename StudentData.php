<?php
session_start();

header("Content-Type: application/json");
header("Cache-Control: no-store");

if (!isset($_SESSION["student_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

echo json_encode([
    "name" => trim(
        ($_SESSION["fname"] ?? "") . " " .
        ($_SESSION["sname"] ?? "")
    ),
    "username" => $_SESSION["username"] ?? ""
]);
?>