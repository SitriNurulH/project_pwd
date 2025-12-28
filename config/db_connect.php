<?php


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_seminar');


$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);


if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}


$conn->set_charset("utf8mb4");

function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $conn->real_escape_string($data);
}

function generate_kode_unik() {
    return 'EVT-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
}
function check_admin_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit();
    }
}


function get_sisa_kuota($event_id) {
    global $conn;
    
    
    $query_event = "SELECT kuota FROM event WHERE event_id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($query_event);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return 0;
    }
    
    $event = $result->fetch_assoc();
    
    
    $query_count = "SELECT COUNT(*) as total FROM pendaftaran 
                    WHERE event_id = ? AND status = 'diterima' AND deleted_at IS NULL";
    $stmt = $conn->prepare($query_count);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();
    
    return max(0, $event['kuota'] - $count['total']);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_nim($nim) {
    return preg_match('/^[0-9]{7,15}$/', $nim);
}

function validate_phone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>