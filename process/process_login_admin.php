<?php
session_start();
require_once '../config/db_connect.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['login_error'] = 'Invalid request method';
    header("Location: ../admin/admin_login.php");
    exit();
}


$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';


if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Username dan password harus diisi';
    header("Location: ../admin/admin_login.php");
    exit();
}


if (strlen($username) < 3) {
    $_SESSION['login_error'] = 'Username minimal 3 karakter';
    header("Location: ../admin/admin_login.php");
    exit();
}


if (strlen($password) < 6) {
    $_SESSION['login_error'] = 'Password minimal 6 karakter';
    header("Location: ../admin/admin_login.php");
    exit();
}


$username = $conn->real_escape_string($username);


$query = "SELECT admin_id, username, password, nama_lengkap 
          FROM admin 
          WHERE username = ? AND deleted_at IS NULL 
          LIMIT 1";

$stmt = $conn->prepare($query);

if (!$stmt) {
    $_SESSION['login_error'] = 'System error. Please try again.';
    header("Location: ../admin/admin_login.php");
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 0) {
    $_SESSION['login_error'] = 'Username atau password salah';
    header("Location: ../admin/admin_login.php");
    exit();
}

$admin = $result->fetch_assoc();


if (!password_verify($password, $admin['password'])) {
    $_SESSION['login_error'] = 'Username atau password salah';
    header("Location: ../admin/admin_login.php");
    exit();
}


$_SESSION['admin_id'] = $admin['admin_id'];
$_SESSION['admin_username'] = $admin['username'];
$_SESSION['admin_nama'] = $admin['nama_lengkap'];
$_SESSION['login_time'] = time();


session_regenerate_id(true);


header("Location: ../admin/admin_dashboard.php");
exit();
?>