<?php

header("Content-Type: application/json");

include "db.php";

$sql = "SELECT
            event_id AS id,
            name,
            date,
            venue,
            category
        FROM events
        ORDER BY event_id";

$result = $conn->query($sql);

$events = [];

while ($row = $result->fetch_assoc()) {

    $row["id"] = (int)$row["id"];

    $events[] = $row;
}

echo json_encode($events);

$conn->close();

?>