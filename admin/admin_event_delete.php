<?php
require_once '../config/db_connect.php';
check_admin_login();

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id <= 0) {
    $_SESSION['error'] = 'ID event tidak valid.';
    header("Location: admin_dashboard.php");
    exit();
}

// Soft delete (hapus event tanpa menghilangkan data)
$query = "UPDATE event SET deleted_at = NOW() WHERE event_id = ? AND deleted_at IS NULL";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = 'Event berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Event tidak ditemukan atau sudah dihapus sebelumnya.';
    }
} else {
    $_SESSION['error'] = 'Gagal menghapus event. Silakan coba lagi.';
}

header("Location: admin_dashboard.php");
exit();
?>
