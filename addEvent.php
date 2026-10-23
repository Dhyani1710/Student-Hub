<?php
require_once "auth.php";
requireRole("admin");

require_once "db.php";

if (empty($_SESSION["event_csrf_token"])) {
    $_SESSION["event_csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";
$messageType = "";

$name = "";
$date = "";
$venue = "";
$category = "";

function safe($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $date = trim($_POST["date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["event_csrf_token"], $token)
    ) {
        $message = "Invalid request. Please refresh the page.";
        $messageType = "error";

    } elseif (
        $name === "" ||
        $date === "" ||
        $venue === "" ||
        $category === ""
    ) {
        $message = "All event details are required.";
        $messageType = "error";

    } elseif (
        strlen($name) > 100 ||
        strlen($venue) > 100 ||
        strlen($category) > 50
    ) {
        $message = "One or more fields exceed the allowed length.";
        $messageType = "error";

    } else {

        $dateObject = DateTime::createFromFormat("!Y-m-d", $date);

        if (
            !$dateObject ||
            $dateObject->format("Y-m-d") !== $date
        ) {
            $message = "Please enter a valid event date.";
            $messageType = "error";

        } elseif (
            !isset($_FILES["poster"]) ||
            $_FILES["poster"]["error"] === UPLOAD_ERR_NO_FILE
        ) {
            $message = "Please select an event poster.";
            $messageType = "error";

        } else {

            $poster = $_FILES["poster"];

            if ($poster["error"] !== UPLOAD_ERR_OK) {

                $message = "Poster upload failed.";
                $messageType = "error";

            } elseif ($poster["size"] > 2 * 1024 * 1024) {

                $message = "Poster size must not exceed 2 MB.";
                $messageType = "error";

            } elseif (
                !is_uploaded_file($poster["tmp_name"])
            ) {

                $message = "Invalid uploaded file.";
                $messageType = "error";

            } else {

                $allowedTypes = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                $finfo = new finfo(FILEINFO_MIME_TYPE);

                $mimeType = $finfo->file(
                    $poster["tmp_name"]
                );

                $imageInfo = @getimagesize(
                    $poster["tmp_name"]
                );

                if (
                    !isset($allowedTypes[$mimeType]) ||
                    $imageInfo === false ||
                    $imageInfo["mime"] !== $mimeType
                ) {

                    $message = "Only valid JPG, JPEG, PNG, and WEBP images are allowed.";
                    $messageType = "error";

                } else {

                    $uploadDirectory = __DIR__ . "/uploads/events/";

                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0755, true);
                    }

                    $extension = $allowedTypes[$mimeType];

                    $fileName = bin2hex(random_bytes(16)) . "." . $extension;

                    $destination = $uploadDirectory . $fileName;

                    if (!move_uploaded_file(
                        $poster["tmp_name"],
                        $destination
                    )) {

                        $message = "Unable to save the event poster.";
                        $messageType = "error";

                    } else {

                        try {

                            $stmt = $conn->prepare(
                                "INSERT INTO events
                                (name, date, venue, category, poster)
                                VALUES (?, ?, ?, ?, ?)"
                            );

                            $stmt->bind_param(
                                "sssss",
                                $name,
                                $date,
                                $venue,
                                $category,
                                $fileName
                            );

                            if ($stmt->execute()) {

                                $_SESSION["event_message"] =
                                    "Event added successfully!";

                                $_SESSION["event_message_type"] =
                                    "success";

                                $stmt->close();
                                $conn->close();

                                header("Location: EventManagement.php");
                                exit();

                            } else {

                                $message = "Failed to save event.";
                                $messageType = "error";

                                unlink($destination);
                            }

                            $stmt->close();

                        } catch (mysqli_sql_exception $e) {

                            error_log($e->getMessage());

                            if (is_file($destination)) {
                                unlink($destination);
                            }

                            $message = "Database error. Event could not be added.";
                            $messageType = "error";
                        }
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Event | StudentHub</title>

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
            text-align: center;
            color: #023e7d;
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

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
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
            margin-top: 6px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Add New Event</h1>

    <?php if ($message !== ""): ?>

        <div class="<?php echo $messageType; ?>">
            <?php echo safe($message); ?>
        </div>

    <?php endif; ?>

    <form
        action="addEvent.php"
        method="POST"
        enctype="multipart/form-data"
    >

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
            value="<?php echo safe($name); ?>"
            required
        >

        <label>Event Date</label>

        <input
            type="date"
            name="date"
            value="<?php echo safe($date); ?>"
            required
        >

        <label>Event Venue</label>

        <input
            type="text"
            name="venue"
            maxlength="100"
            value="<?php echo safe($venue); ?>"
            required
        >

        <label>Event Category</label>

        <select name="category" required>

            <option value="">Select Category</option>

            <?php
            $categories = [
                "Technical",
                "Sports",
                "Cultural",
                "Educational",
                "Workshop",
                "Other"
            ];

            foreach ($categories as $item):
            ?>

                <option
                    value="<?php echo safe($item); ?>"
                    <?php echo $category === $item ? "selected" : ""; ?>
                >
                    <?php echo safe($item); ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Event Poster</label>

        <input
            type="file"
            name="poster"
            accept=".jpg,.jpeg,.png,.webp"
            required
        >

        <p class="hint">
            Allowed: JPG, JPEG, PNG, WEBP | Maximum size: 2 MB
        </p>

        <button type="submit">
            Add Event
        </button>

        <a href="EventManagement.php" class="btn">
            Back
        </a>

    </form>

</div>

</body>
</html>

<?php
$conn->close();
?>