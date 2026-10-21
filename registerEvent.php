<?php

session_start();

header("Content-Type: application/json");

include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

if (!isset($_SESSION["student_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Please login before registering."
    ]);
    exit();
}

$student_id = (int) $_SESSION["student_id"];
$event_id = filter_input(INPUT_POST, "event_id", FILTER_VALIDATE_INT);

if (!$event_id || $event_id < 1) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid event selected."
    ]);
    exit();
}

$sql = "INSERT INTO registrations (student_id, event_id)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $student_id, $event_id);

try {

    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Event Registration Successful!"
    ]);

} catch (mysqli_sql_exception $e) {

    if ($e->getCode() == 1062) {
        http_response_code(409);

        echo json_encode([
            "success" => false,
            "message" => "You have already registered for this event."
        ]);

    } elseif ($e->getCode() == 1452) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Selected event does not exist."
        ]);

    } else {
        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Registration failed. Please try again."
        ]);
    }
}

$stmt->close();
$conn->close();

?>