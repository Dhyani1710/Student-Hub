<?php
require_once "auth.php";
requireRole("admin");

require_once "db.php";

if (empty($_SESSION["event_csrf_token"])) {
    $_SESSION["event_csrf_token"] = bin2hex(random_bytes(32));
}

function safe($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: EventManagement.php");
    exit();
}

$stmt = $conn->prepare(
    "SELECT event_id, name, date, venue, category, poster
     FROM events
     WHERE event_id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    http_response_code(404);
    exit("Event not found.");
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $date = trim($_POST["date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $token = $_POST["csrf_token"] ?? "";

    $event["name"] = $name;
    $event["date"] = $date;
    $event["venue"] = $venue;
    $event["category"] = $category;

    $newPoster = null;
    $uploadPath = __DIR__ . "/uploads/events/";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["event_csrf_token"], $token)
    ) {
        $message = "Invalid request. Please refresh the page.";

    } elseif (
        $name === "" ||
        $date === "" ||
        $venue === "" ||
        $category === ""
    ) {
        $message = "All event details are required.";

    } elseif (
        strlen($name) > 100 ||
        strlen($venue) > 100 ||
        strlen($category) > 50
    ) {
        $message = "One or more fields are too long.";

    } else {

        $dateObject = DateTime::createFromFormat("!Y-m-d", $date);

        if (
            !$dateObject ||
            $dateObject->format("Y-m-d") !== $date
        ) {
            $message = "Invalid event date.";
        }

        $hasNewPoster =
            isset($_FILES["poster"]) &&
            $_FILES["poster"]["error"] !== UPLOAD_ERR_NO_FILE;

        if ($message === "" && $hasNewPoster) {

            $poster = $_FILES["poster"];

            if ($poster["error"] !== UPLOAD_ERR_OK) {
                $message = "Poster upload failed.";

            } elseif ($poster["size"] > 2 * 1024 * 1024) {
                $message = "Poster must be smaller than 2 MB.";

            } elseif (!is_uploaded_file($poster["tmp_name"])) {
                $message = "Invalid uploaded file.";

            } else {

                $allowedTypes = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($poster["tmp_name"]);
                $imageInfo = @getimagesize($poster["tmp_name"]);

                if (
                    !isset($allowedTypes[$mimeType]) ||
                    $imageInfo === false ||
                    $imageInfo["mime"] !== $mimeType
                ) {
                    $message = "Only JPG, JPEG, PNG and WEBP images are allowed.";

                } else {

                    $newPoster = bin2hex(random_bytes(16))
                        . "." . $allowedTypes[$mimeType];

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    if (!move_uploaded_file(
                        $poster["tmp_name"],
                        $uploadPath . $newPoster
                    )) {
                        $message = "Failed to save new poster.";
                        $newPoster = null;
                    }
                }
            }
        }

        if ($message === "") {

            $oldPoster = $event["poster"];
            $posterName = $newPoster ?? $oldPoster;

            try {

                $update = $conn->prepare(
                    "UPDATE events
                     SET name = ?,
                         date = ?,
                         venue = ?,
                         category = ?,
                         poster = ?
                     WHERE event_id = ?"
                );

                $update->bind_param(
                    "sssssi",
                    $name,
                    $date,
                    $venue,
                    $category,
                    $posterName,
                    $id
                );

                $update->execute();
                $update->close();

                if ($newPoster !== null && !empty($oldPoster)) {

                    $oldPath = $uploadPath . basename($oldPoster);

                    if (is_file($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $_SESSION["event_message"] =
                    "Event updated successfully!";

                $_SESSION["event_message_type"] = "success";

                header("Location: EventManagement.php");
                exit();

            } catch (mysqli_sql_exception $e) {

                error_log($e->getMessage());

                if ($newPoster !== null) {
                    $newPath = $uploadPath . $newPoster;

                    if (is_file($newPath)) {
                        unlink($newPath);
                    }
                }

                $message = "Failed to update event.";
            }
        }
    }

    if ($message !== "") {
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Event | StudentHub</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 550px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: #023e7d;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 18px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .poster-preview {
            width: 180px;
            max-height: 220px;
            object-fit: contain;
            margin-top: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        button, .btn {
            display: inline-block;
            background: #0466c8;
            color: white;
            border: none;
            padding: 12px 18px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
        }

        button:hover, .btn:hover {
            background: #023e7d;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .hint {
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Edit Event</h1>

    <?php if ($message !== ""): ?>
        <div class="error">
            <?php echo safe($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo safe($_SESSION["event_csrf_token"]); ?>"
        >

        <label>Event Name</label>

        <input
            type="text"
            name="name"
            maxlength="100"
            value="<?php echo safe($event["name"]); ?>"
            required
        >

        <label>Event Date</label>

        <input
            type="date"
            name="date"
            value="<?php echo safe($event["date"]); ?>"
            required
        >

        <label>Event Venue</label>

        <input
            type="text"
            name="venue"
            maxlength="100"
            value="<?php echo safe($event["venue"]); ?>"
            required
        >

        <label>Event Category</label>

        <input
            type="text"
            name="category"
            maxlength="50"
            value="<?php echo safe($event["category"]); ?>"
            required
        >

        <label>Current Poster</label>

        <?php if (!empty($event["poster"])): ?>

            <br>

            <img
                src="uploads/events/<?php echo rawurlencode(basename($event["poster"])); ?>"
                class="poster-preview"
                alt="Current Event Poster"
            >

        <?php else: ?>

            <p>No poster uploaded.</p>

        <?php endif; ?>

        <label>Upload New Poster (Optional)</label>

        <input
            type="file"
            name="poster"
            accept=".jpg,.jpeg,.png,.webp"
        >

        <p class="hint">
            Leave empty to keep the existing poster.
            Maximum size: 2 MB.
        </p>

        <button type="submit">
            Update Event
        </button>

        <a href="EventManagement.php" class="btn">
            Back
        </a>

    </form>

</div>

</body>
</html>