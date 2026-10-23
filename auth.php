<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout = 900;

if (!isset($_SESSION["student_id"])) {
    header("Location: StudentLogin.html");
    exit();
}

if (
    isset($_SESSION["last_activity"]) &&
    time() - $_SESSION["last_activity"] > $timeout
) {

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    header("Location: StudentLogin.html");
    exit();
}

$_SESSION["last_activity"] = time();

function requireRole($role) {

    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== $role) {
        http_response_code(403);
        exit("Access Denied!");
    }
}

?>