<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

$response = [
    'success' => false,
    'events' => [],
    'message' => ''
];

try {
    
    $search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
    
    
    $query = "SELECT e.*, 
              COUNT(p.daftar_id) as jumlah_pendaftar
              FROM event e
              LEFT JOIN pendaftaran p ON e.event_id = p.event_id 
                  AND p.status = 'diterima' 
                  AND p.deleted_at IS NULL
              WHERE e.deleted_at IS NULL";
    
    
    if (!empty($search)) {
        $query .= " AND (e.nama_event LIKE ? OR e.lokasi LIKE ? OR e.narasumber LIKE ?)";
    }
    
    $query .= " GROUP BY e.event_id ORDER BY e.tanggal ASC";
    
    
    if ($limit > 0) {
        $query .= " LIMIT ?";
    }
    
    
    $stmt = $conn->prepare($query);
    
    if (!empty($search)) {
        $searchParam = "%{$search}%";
        if ($limit > 0) {
            $stmt->bind_param("sssi", $searchParam, $searchParam, $searchParam, $limit);
        } else {
            $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
        }
    } else {
        if ($limit > 0) {
            $stmt->bind_param("i", $limit);
        }
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'event_id' => $row['event_id'],
            'nama_event' => $row['nama_event'],
            'tanggal' => $row['tanggal'],
            'waktu' => date('H:i', strtotime($row['waktu'])),
            'lokasi' => $row['lokasi'],
            'narasumber' => $row['narasumber'],
            'deskripsi' => $row['deskripsi'],
            'kuota' => intval($row['kuota']),
            'jumlah_pendaftar' => intval($row['jumlah_pendaftar']),
            'banner' => $row['banner']
        ];
    }
    
    $response['success'] = true;
    $response['events'] = $events;
    $response['total'] = count($events);
    
} catch (Exception $e) {
    $response['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

echo json_encode($response);
?>