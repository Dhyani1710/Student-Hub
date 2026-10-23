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
    !isset($_SESSION["event_csrf_token"]) ||
    !is_string($token) ||
    !hash_equals($_SESSION["event_csrf_token"], $token)
) {
    http_response_code(400);
    exit("Invalid deletion request.");
}

try {

    $conn->begin_transaction();

    $stmt = $conn->prepare(
        "SELECT poster FROM events
         WHERE event_id = ?
         FOR UPDATE"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $event = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$event) {

        $conn->rollback();

        $_SESSION["event_message"] =
            "Event not found.";

        $_SESSION["event_message_type"] = "error";

    } else {

        $stmt = $conn->prepare(
            "DELETE FROM events WHERE event_id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $deleted = $stmt->affected_rows > 0;
        $stmt->close();

        $conn->commit();

        if ($deleted) {

            if (!empty($event["poster"])) {

                $posterPath = __DIR__
                    . "/uploads/events/"
                    . basename($event["poster"]);

                if (is_file($posterPath)) {
                    unlink($posterPath);
                }
            }

            $_SESSION["event_message"] =
                "Event deleted successfully!";

            $_SESSION["event_message_type"] = "success";

        } else {

            $_SESSION["event_message"] =
                "Event could not be deleted.";

            $_SESSION["event_message_type"] = "error";
        }
    }

} catch (mysqli_sql_exception $e) {

    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {
        error_log($rollbackError->getMessage());
    }

    error_log($e->getMessage());

    $_SESSION["event_message"] =
        "Failed to delete event. It may have student registrations.";

    $_SESSION["event_message_type"] = "error";
}

$conn->close();

header("Location: EventManagement.php");
exit();
?>