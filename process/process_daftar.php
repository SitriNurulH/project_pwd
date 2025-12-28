<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$response = [
    'success' => false,
    'message' => '',
    'errors' => [],
    'kode_unik' => ''
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    $nama = isset($_POST['nama']) ? clean_input($_POST['nama']) : '';
    $nim = isset($_POST['nim']) ? clean_input($_POST['nim']) : '';
    $email = isset($_POST['email']) ? clean_input($_POST['email']) : '';
    $no_hp = isset($_POST['no_hp']) ? clean_input($_POST['no_hp']) : '';
    $jurusan = isset($_POST['jurusan']) ? clean_input($_POST['jurusan']) : '';

    $errors = [];

    
    if (empty($nama)) {
        $errors['nama'] = 'Nama lengkap harus diisi';
    } elseif (strlen($nama) < 3) {
        $errors['nama'] = 'Nama minimal 3 karakter';
    }

    if (empty($nim)) {
        $errors['nim'] = 'NIM harus diisi';
    } elseif (!preg_match('/^[0-9]{7,15}$/', $nim)) {
        $errors['nim'] = 'NIM tidak valid (hanya angka, 7-15 digit)';
    }

    if (empty($email)) {
        $errors['email'] = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid';
    }

    if (empty($no_hp)) {
        $errors['no_hp'] = 'No. HP harus diisi';
    } elseif (!preg_match('/^[0-9]{10,15}$/', $no_hp)) {
        $errors['no_hp'] = 'No. HP tidak valid (hanya angka, 10-15 digit)';
    }

    if ($event_id <= 0) {
        throw new Exception('Event tidak valid');
    }

    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = 'Silakan perbaiki input yang salah';
        echo json_encode($response);
        exit();
    }

    
    $query_event = "SELECT * FROM event WHERE event_id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($query_event);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result_event = $stmt->get_result();

    if ($result_event->num_rows == 0) {
        throw new Exception('Event tidak ditemukan');
    }

    $event = $result_event->fetch_assoc();

    
    $sisa_kuota = get_sisa_kuota($event_id);
    if ($sisa_kuota <= 0) {
        throw new Exception('Maaf, kuota event sudah penuh');
    }

    
    $conn->begin_transaction();

    try {
        
        $query_peserta = "SELECT peserta_id FROM peserta WHERE nim = ? AND deleted_at IS NULL";
        $stmt = $conn->prepare($query_peserta);
        $stmt->bind_param("s", $nim);
        $stmt->execute();
        $result_peserta = $stmt->get_result();

        if ($result_peserta->num_rows > 0) {
            
            $peserta = $result_peserta->fetch_assoc();
            $peserta_id = $peserta['peserta_id'];

            
            $query_update = "UPDATE peserta SET nama = ?, email = ?, no_hp = ?, jurusan = ? WHERE peserta_id = ?";
            $stmt = $conn->prepare($query_update);
            $stmt->bind_param("ssssi", $nama, $email, $no_hp, $jurusan, $peserta_id);
            $stmt->execute();
        } else {
            
            $query_insert = "INSERT INTO peserta (nama, nim, email, no_hp, jurusan) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query_insert);
            $stmt->bind_param("sssss", $nama, $nim, $email, $no_hp, $jurusan);
            $stmt->execute();
            $peserta_id = $conn->insert_id;
        }

        
        $query_check = "SELECT daftar_id FROM pendaftaran 
                        WHERE peserta_id = ? AND event_id = ? AND deleted_at IS NULL";
        $stmt = $conn->prepare($query_check);
        $stmt->bind_param("ii", $peserta_id, $event_id);
        $stmt->execute();
        $result_check = $stmt->get_result();

        if ($result_check->num_rows > 0) {
            throw new Exception('Anda sudah terdaftar di event ini');
        }

        
        $kode_unik = generate_kode_unik();

        
        $query_daftar = "INSERT INTO pendaftaran (peserta_id, event_id, status, kode_unik) 
                        VALUES (?, ?, 'pending', ?)";
        $stmt = $conn->prepare($query_daftar);
        $stmt->bind_param("iis", $peserta_id, $event_id, $kode_unik);
        $stmt->execute();

        
        $conn->commit();

        $response['success'] = true;
        $response['message'] = 'Pendaftaran berhasil! Silakan simpan kode pendaftaran Anda.';
        $response['kode_unik'] = $kode_unik;

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>