<?php
require_once '../config/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

// ambil parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;

try {
    // base query: ambil event + jumlah pendaftar
    $sql = "SELECT e.*,
                (
                    SELECT COUNT(*)
                    FROM pendaftaran p
                    WHERE p.event_id = e.event_id
                    AND p.deleted_at IS NULL
                ) AS jumlah_pendaftar
            FROM event e
            WHERE e.deleted_at IS NULL";

    $params = [];
    $types  = "";

    // search
    if (!empty($search)) {
        $sql .= " AND (
                    e.nama_event LIKE ?
                    OR e.lokasi LIKE ?
                    OR e.narasumber LIKE ?
                 )";
        $like = "%$search%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $types .= "sss";
    }

    // sorting (event terdekat dulu)
    $sql .= " ORDER BY e.tanggal ASC, e.waktu ASC";

    // limit
    if ($limit > 0) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
        $types .= "i";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare gagal: " . $conn->error);
    }

    // bind parameter jika ada
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $events = [];
    while ($row = $result->fetch_assoc()) {
        // pastikan angka jadi integer
        $row['kuota'] = (int)$row['kuota'];
        $row['jumlah_pendaftar'] = (int)$row['jumlah_pendaftar'];
        $events[] = $row;
    }

    echo json_encode([
        "success" => true,
        "events"  => $events
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Gagal memuat data event",
        "error"   => $e->getMessage()
    ]);
}
