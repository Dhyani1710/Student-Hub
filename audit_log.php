<?php

function addAuditLog($conn, $adminId, $action, $details = "") {

    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (admin_id, action, details)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "iss",
        $adminId,
        $action,
        $details
    );

    $stmt->execute();
    $stmt->close();
}
?>