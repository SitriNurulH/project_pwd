<?php
/**
 * Process Login Admin
 * Validasi username & password dengan hashing
 */

session_start();
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/admin_login.php");
    exit();
}

// Ambil data dari form
$username = isset($_POST['username']) ? clean_input($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : ''; // Tidak di-clean untuk password

// Validasi input
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Username dan password harus diisi';
    header("Location: ../admin/admin_login.php");
    exit();
}

// Query admin berdasarkan username
$query = "SELECT * FROM admin WHERE username = ? AND deleted_at IS NULL LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login_error'] = 'Username atau password salah';
    header("Location: ../admin/admin_login.php");
    exit();
}

$admin = $result->fetch_assoc();

// Verifikasi password dengan password_verify()
if (!password_verify($password, $admin['password'])) {
    $_SESSION['login_error'] = 'Username atau password salah';
    header("Location: ../admin/admin_login.php");
    exit();
}

// Login berhasil, set session
$_SESSION['admin_id'] = $admin['admin_id'];
$_SESSION['admin_username'] = $admin['username'];
$_SESSION['admin_nama'] = $admin['nama_lengkap'];
$_SESSION['login_time'] = time();

// Redirect ke dashboard
header("Location: ../admin/admin_dashboard.php");
exit();
?>