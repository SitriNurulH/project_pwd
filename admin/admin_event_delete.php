<?php 
/**
 * Soft Delete Event
 * Set deleted_at timestamp instead of hard delete
 */

require_once '../config/db_connect.php';
check_admin_login();

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id <= 0) {
    $_SESSION['error'] = 'ID event tidak valid';
    header("Location: admin_dashboard.php");
    exit();
}

// Soft delete: set deleted_at timestamp
$query = "UPDATE event SET deleted_at = NOW() WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Event berhasil dihapus';
} else {
    $_SESSION['error'] = 'Gagal menghapus event';
}

header("Location: admin_dashboard.php");
exit();
?>