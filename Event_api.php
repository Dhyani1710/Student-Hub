<?php
require_once "auth.php";
requireRole("admin");

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store");

function respond($status, $message, $data = null) {
    http_response_code($status);

    echo json_encode([
        "success" => $status >= 200 && $status < 300,
        "message" => $message,
        "data" => $data
    ]);

    exit();
}

function validDate($date) {
    if (!is_string($date)) {
        return false;
    }

    $d = DateTime::createFromFormat("!Y-m-d", $date);

    return $d && $d->format("Y-m-d") === $date;
}

function getInput() {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        respond(400, "Invalid JSON data.");
    }

    return $input;
}

function checkEvent($data) {
    $name = trim($data["name"] ?? "");
    $date = trim($data["date"] ?? "");
    $venue = trim($data["venue"] ?? "");
    $category = trim($data["category"] ?? "");

    if ($name === "" || $date === "" ||
        $venue === "" || $category === "") {
        respond(400, "All fields are required.");
    }

    if (
        strlen($name) > 100 ||
        strlen($venue) > 100 ||
        strlen($category) > 50
    ) {
        respond(400, "One or more fields are too long.");
    }

    if (!validDate($date)) {
        respond(400, "Invalid event date.");
    }

    return [$name, $date, $venue, $category];
}

function eventId() {
    $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

    if (!$id || $id <= 0) {
        respond(400, "Valid event ID is required.");
    }

    return $id;
}

$method = $_SERVER["REQUEST_METHOD"];

try {

    if ($method === "GET") {

        $stmt = $conn->prepare(
            "SELECT event_id, name, date, venue, category, poster
             FROM events
             ORDER BY event_id DESC"
        );

        $stmt->execute();

        $result = $stmt->get_result();
        $events = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        respond(200, "Events loaded successfully.", $events);
    }

    if ($method === "POST") {

        $data = getInput();

        [$name, $date, $venue, $category] = checkEvent($data);

        $stmt = $conn->prepare(
            "INSERT INTO events (name, date, venue, category)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $name,
            $date,
            $venue,
            $category
        );

        $stmt->execute();

        $id = $conn->insert_id;

        $stmt->close();

        respond(201, "Event added successfully.", [
            "event_id" => $id
        ]);
    }

    if ($method === "PUT") {

        $id = eventId();
        $data = getInput();

        [$name, $date, $venue, $category] = checkEvent($data);

        $stmt = $conn->prepare(
            "UPDATE events
             SET name = ?, date = ?, venue = ?, category = ?
             WHERE event_id = ?"
        );

        $stmt->bind_param(
            "ssssi",
            $name,
            $date,
            $venue,
            $category,
            $id
        );

        $stmt->execute();

        $stmt->close();

        respond(200, "Event updated successfully.");
    }

    if ($method === "DELETE") {

        $id = eventId();

        $stmt = $conn->prepare(
            "DELETE FROM events WHERE event_id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $stmt->close();
            respond(404, "Event not found.");
        }

        $stmt->close();

        respond(200, "Event deleted successfully.");
    }

    respond(405, "Method not allowed.");

} catch (mysqli_sql_exception $e) {

    error_log($e->getMessage());

    respond(500, "Database operation failed.");
}
?>