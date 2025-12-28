<?php 
require_once '../config/db_connect.php';
check_admin_login();

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;


$query_events = "SELECT event_id, nama_event FROM event WHERE deleted_at IS NULL ORDER BY tanggal DESC";
$result_events = $conn->query($query_events);


if ($event_id > 0) {
    $query = "SELECT pf.*, ps.nama, ps.nim, ps.email, ps.no_hp, ps.jurusan, e.nama_event 
              FROM pendaftaran pf
              JOIN peserta ps ON pf.peserta_id = ps.peserta_id
              JOIN event e ON pf.event_id = e.event_id
              WHERE pf.event_id = ? AND pf.deleted_at IS NULL
              ORDER BY pf.tanggal_daftar DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result_pendaftaran = $stmt->get_result();
} else {
    $query = "SELECT pf.*, ps.nama, ps.nim, ps.email, ps.no_hp, ps.jurusan, e.nama_event 
              FROM pendaftaran pf
              JOIN peserta ps ON pf.peserta_id = ps.peserta_id
              JOIN event e ON pf.event_id = e.event_id
              WHERE pf.deleted_at IS NULL
              ORDER BY pf.tanggal_daftar DESC
              LIMIT 50";
    $result_pendaftaran = $conn->query($query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pendaftaran - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--dark); color: var(--white); padding: 2rem 0; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar-brand { padding: 0 1.5rem; margin-bottom: 2rem; }
        .sidebar-brand h2 { font-size: 1.3rem; color: var(--white); }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 0.5rem; }
        .sidebar-menu a { display: block; padding: 0.75rem 1.5rem; color: var(--white); text-decoration: none; transition: all 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: var(--primary-color); }
        .main-content { flex: 1; margin-left: 250px; padding: 2rem; background: var(--light); }
        .filter-container { background: var(--white); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>📅 Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php">📊 Dashboard</a></li>
                <li><a href="admin_event_add.php">➕ Tambah Event</a></li>
                <li><a href="admin_verifikasi.php" class="active"> Verifikasi Pendaftaran</a></li>
                <li><a href="../process/process_logout.php" style="color: var(--danger-color);">🚪 Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">Verifikasi Pendaftaran</h1>

            <div class="filter-container">
                <form method="GET" style="display: flex; gap: 1rem; align-items: end;">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="event_id">Filter Berdasarkan Event</label>
                        <select id="event_id" name="event_id" class="form-control">
                            <option value="0">Semua Event</option>
                            <?php while ($event = $result_events->fetch_assoc()): ?>
                                <option value="<?php echo $event['event_id']; ?>" 
                                        <?php echo $event_id == $event['event_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($event['nama_event']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>

            <div style="background: var(--white); padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Email</th>
                                <th>Event</th>
                                <th>Tanggal Daftar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_pendaftaran->num_rows > 0): ?>
                                <?php while ($row = $result_pendaftaran->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['kode_unik']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_event']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_daftar'])); ?></td>
                                        <td>
                                            <?php if ($row['status'] === 'pending'): ?>
                                                <span class="badge badge-pending">Pending</span>
                                            <?php elseif ($row['status'] === 'diterima'): ?>
                                                <span class="badge badge-success">Diterima</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Ditolak</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['status'] === 'pending'): ?>
                                                <button onclick="updateStatus(<?php echo $row['daftar_id']; ?>, 'diterima')" 
                                                        class="btn btn-success" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-right: 0.25rem;">
                                                    Terima
                                                </button>
                                                <button onclick="updateStatus(<?php echo $row['daftar_id']; ?>, 'ditolak')" 
                                                        class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                    Tolak
                                                </button>
                                            <?php else: ?>
                                                <button onclick="updateStatus(<?php echo $row['daftar_id']; ?>, 'pending')" 
                                                        class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                    Reset
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center;">Tidak ada pendaftaran</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        function updateStatus(daftarId, status) {
            if (!confirm('Yakin ingin mengubah status pendaftaran ini?')) {
                return;
            }

            fetch('../process/process_verifikasi.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `daftar_id=${daftarId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        }
    </script>
</body>
</html>