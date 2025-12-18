<?php 
require_once '../config/db_connect.php';
check_admin_login();

// Get statistics
$query_total_event = "SELECT COUNT(*) as total FROM event WHERE deleted_at IS NULL";
$total_event = $conn->query($query_total_event)->fetch_assoc()['total'];

$query_total_peserta = "SELECT COUNT(DISTINCT peserta_id) as total FROM pendaftaran WHERE deleted_at IS NULL";
$total_peserta = $conn->query($query_total_peserta)->fetch_assoc()['total'];

$query_pending = "SELECT COUNT(*) as total FROM pendaftaran WHERE status = 'pending' AND deleted_at IS NULL";
$total_pending = $conn->query($query_pending)->fetch_assoc()['total'];

// Get recent events
$query_events = "SELECT e.*, COUNT(p.daftar_id) as jumlah_pendaftar 
                 FROM event e 
                 LEFT JOIN pendaftaran p ON e.event_id = p.event_id AND p.deleted_at IS NULL
                 WHERE e.deleted_at IS NULL 
                 GROUP BY e.event_id 
                 ORDER BY e.tanggal DESC 
                 LIMIT 5";
$result_events = $conn->query($query_events);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistem Event Kampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: var(--dark);
            color: var(--white);
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }
        .sidebar-brand h2 {
            font-size: 1.3rem;
            color: var(--white);
        }
        .sidebar-menu {
            list-style: none;
        }
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        .sidebar-menu a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: var(--white);
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--primary-color);
        }
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
            background: var(--light);
        }
        .admin-header {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-icon {
            font-size: 2.5rem;
        }
        .stat-info h3 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }
        .stat-info p {
            color: #6b7280;
            font-size: 0.9rem;
        }
        .content-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .logout-btn {
            background: var(--danger-color);
            color: var(--white);
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }
        .logout-btn:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>📅 Admin Panel</h2>
                <p style="font-size: 0.9rem; color: #9ca3af; margin-top: 0.5rem;">
                    <?php echo htmlspecialchars($_SESSION['admin_nama']); ?>
                </p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="admin_event_add.php">➕ Tambah Event</a></li>
                <li><a href="admin_verifikasi.php">✅ Verifikasi Pendaftaran</a></li>
                <li><a href="../public/index.php" target="_blank">🌐 Lihat Website</a></li>
                <li><a href="../process/process_logout.php" style="color: var(--danger-color);">🚪 Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="admin-header">
                <div>
                    <h1>Dashboard</h1>
                    <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_nama']); ?>!</p>
                </div>
                <a href="../process/process_logout.php" class="logout-btn">Logout</a>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-icon">📅</span>
                    <div class="stat-info">
                        <h3><?php echo $total_event; ?></h3>
                        <p>Total Event</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">👥</span>
                    <div class="stat-info">
                        <h3><?php echo $total_peserta; ?></h3>
                        <p>Total Peserta</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">⏳</span>
                    <div class="stat-info">
                        <h3><?php echo $total_pending; ?></h3>
                        <p>Pending Approval</p>
                    </div>
                </div>
            </div>

            <!-- Event List -->
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2>Daftar Event</h2>
                    <a href="admin_event_add.php" class="btn btn-primary">+ Tambah Event</a>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Event</th>
                                <th>Tanggal</th>
                                <th>Lokasi</th>
                                <th>Kuota</th>
                                <th>Pendaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_events->num_rows > 0): ?>
                                <?php while ($event = $result_events->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($event['nama_event']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($event['tanggal'])); ?></td>
                                        <td><?php echo htmlspecialchars($event['lokasi']); ?></td>
                                        <td><?php echo $event['kuota']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $event['jumlah_pendaftar'] >= $event['kuota'] ? 'badge-danger' : 'badge-success'; ?>">
                                                <?php echo $event['jumlah_pendaftar']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="admin_verifikasi.php?event_id=<?php echo $event['event_id']; ?>" 
                                               class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.9rem;">
                                                Lihat Peserta
                                            </a>
                                            <a href="admin_event_delete.php?id=<?php echo $event['event_id']; ?>" 
                                               class="btn btn-danger" style="padding: 0.4rem 1rem; font-size: 0.9rem;"
                                               onclick="return confirm('Yakin ingin menghapus event ini?')">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Belum ada event</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>