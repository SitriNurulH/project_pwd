<?php

header('Content-Type: application/json');
session_start();
require_once '../config/db_connect.php';

$response = [
    'success' => false,
    'message' => ''
];

try {
    
    if (!isset($_SESSION['admin_id'])) {
        throw new Exception('Unauthorized access');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $daftar_id = isset($_POST['daftar_id']) ? intval($_POST['daftar_id']) : 0;
    $status = isset($_POST['status']) ? clean_input($_POST['status']) : '';

    
    if ($daftar_id <= 0) {
        throw new Exception('ID pendaftaran tidak valid');
    }

    if (!in_array($status, ['pending', 'diterima', 'ditolak'])) {
        throw new Exception('Status tidak valid');
    }

    
    if ($status === 'diterima') {
        
        $query_check = "SELECT event_id FROM pendaftaran WHERE daftar_id = ?";
        $stmt = $conn->prepare($query_check);
        $stmt->bind_param("i", $daftar_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Pendaftaran tidak ditemukan');
        }
        
        $pendaftaran = $result->fetch_assoc();
        $event_id = $pendaftaran['event_id'];
        
        
        $sisa_kuota = get_sisa_kuota($event_id);
        if ($sisa_kuota <= 0) {
            throw new Exception('Kuota event sudah penuh');
        }
    }

    
    $query = "UPDATE pendaftaran SET status = ? WHERE daftar_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $daftar_id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Status pendaftaran berhasil diperbarui';
    } else {
        throw new Exception('Gagal memperbarui status');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>