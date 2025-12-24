<?php

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_seminar');

// Koneksi ke database menggunakan MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset ke UTF-8
$conn->set_charset("utf8mb4");

/**
 * Fungsi untuk mencegah SQL Injection
 */
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $conn->real_escape_string($data);
}

/**
 * Fungsi untuk menghasilkan kode unik pendaftaran
 */
function generate_kode_unik() {
    return 'EVT-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
}

/**
 * Fungsi untuk cek login admin
 */
function check_admin_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit();
    }
}

/**
 * Fungsi untuk menghitung sisa kuota event
 */
function get_sisa_kuota($event_id) {
    global $conn;
    
    // Ambil kuota event
    $query_event = "SELECT kuota FROM event WHERE event_id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($query_event);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return 0;
    }
    
    $event = $result->fetch_assoc();
    
    // Hitung jumlah pendaftar yang diterima
    $query_count = "SELECT COUNT(*) as total FROM pendaftaran 
                    WHERE event_id = ? AND status = 'diterima' AND deleted_at IS NULL";
    $stmt = $conn->prepare($query_count);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();
    
    return max(0, $event['kuota'] - $count['total']);
}

/**
 * Fungsi untuk validasi email
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Fungsi untuk validasi NIM
 */
function validate_nim($nim) {
    return preg_match('/^[0-9]{7,15}$/', $nim);
}

/**
 * Fungsi untuk validasi nomor HP
 */
function validate_phone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>