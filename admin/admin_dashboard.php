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

$query_diterima = "SELECT COUNT(*) as total FROM pendaftaran WHERE status = 'diterima' AND deleted_at IS NULL";
$total_diterima = $conn->query($query_diterima)->fetch_assoc()['total'];

// Get recent events
$query_events = "SELECT e.*, COUNT(p.daftar_id) as jumlah_pendaftar 
                 FROM event e 
                 LEFT JOIN pendaftaran p ON e.event_id = p.event_id AND p.deleted_at IS NULL
                 WHERE e.deleted_at IS NULL 
                 GROUP BY e.event_id 
                 ORDER BY e.tanggal DESC 
                 LIMIT 10";
$result_events = $conn->query($query_events);

$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Admin Dashboard - Sistem Event Kampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>💖 Admin Panel</h2>
                <p><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="admin_event_add.php">➕ Tambah Event</a></li>
                <li><a href="admin_verifikasi.php">✅ Verifikasi Pendaftaran</a></li>
                <li><a href="../public/index.php" target="_blank">🌐 Lihat Website</a></li>
                <li><a href="../process/process_logout.php" style="color: #fff0f5;">🚪 Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="admin-header">
                <div>
                    <h1>👋 Dashboard Admin</h1>
                    <p>Selamat datang kembali, <strong><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></strong>!</p>
                </div>
                <a href="../process/process_logout.php" class="btn btn-danger" style="padding: 0.75rem 1.5rem;">
                    🚪 Logout
                </a>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <strong>✅ Berhasil!</strong> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <strong>❌ Error!</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
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
                <div class="stat-card">
                    <span class="stat-icon">✅</span>
                    <div class="stat-info">
                        <h3><?php echo $total_diterima; ?></h3>
                        <p>Pendaftar Diterima</p>
                    </div>
                </div>
            </div>

            <!-- Event List -->
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 3px solid var(--soft-pink);">
                    <h2 style="color: var(--primary-pink); font-size: 1.8rem; font-weight: 700;">
                        📋 Daftar Event
                    </h2>
                    <a href="admin_event_add.php" class="btn btn-primary">
                        ➕ Tambah Event Baru
                    </a>
                </div>

                <?php if ($result_events->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 40%;">📌 Nama Event</th>
                                    <th>📅 Tanggal</th>
                                    <th>📍 Lokasi</th>
                                    <th style="text-align: center;">👥 Kuota</th>
                                    <th style="text-align: center;">📊 Pendaftar</th>
                                    <th style="text-align: center;">⚙️ Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($event = $result_events->fetch_assoc()): ?>
                                    <?php 
                                    $percentage = ($event['jumlah_pendaftar'] / $event['kuota']) * 100;
                                    $status_class = $percentage >= 100 ? 'danger' : ($percentage >= 75 ? 'warning' : 'success');
                                    ?>
                                    <tr>
                                        <td>
                                            <strong style="color: var(--primary-pink);">
                                                <?php echo htmlspecialchars($event['nama_event']); ?>
                                            </strong>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($event['tanggal'])); ?></td>
                                        <td><?php echo htmlspecialchars($event['lokasi']); ?></td>
                                        <td style="text-align: center; font-weight: 600;">
                                            <?php echo $event['kuota']; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge badge-<?php echo $status_class; ?>">
                                                <?php echo $event['jumlah_pendaftar']; ?> / <?php echo $event['kuota']; ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="admin_verifikasi.php?event_id=<?php echo $event['event_id']; ?>" 
                                               class="btn btn-secondary" 
                                               style="padding: 0.5rem 1rem; font-size: 0.9rem; margin-right: 0.25rem;">
                                                👁️ Lihat
                                            </a>
                                            <a href="admin_event_delete.php?id=<?php echo $event['event_id']; ?>" 
                                               class="btn btn-danger" 
                                               style="padding: 0.5rem 1rem; font-size: 0.9rem;"
                                               onclick="return confirm('⚠️ Yakin ingin menghapus event ini?\n\nEvent: <?php echo htmlspecialchars($event['nama_event']); ?>')">
                                                🗑️ Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <p style="font-size: 3rem; margin-bottom: 1rem;">📭</p>
                        <p style="font-size: 1.2rem; font-weight: 600; color: var(--primary-pink);">Belum ada event</p>
                        <p style="color: var(--text-light); margin-top: 0.5rem;">Klik tombol "Tambah Event Baru" untuk menambahkan event pertama!</p>
                        <a href="admin_event_add.php" class="btn btn-primary" style="margin-top: 1.5rem;">
                            ➕ Tambah Event Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Info -->
            <div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <div style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: var(--shadow-md); border-left: 5px solid var(--primary-pink);">
                    <h4 style="color: var(--primary-pink); margin-bottom: 0.5rem; font-size: 1.2rem;">📌 Tips</h4>
                    <p style="color: var(--text-light); font-size: 0.95rem;">Verifikasi pendaftaran segera agar peserta mendapat konfirmasi!</p>
                </div>
                <div style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: var(--shadow-md); border-left: 5px solid var(--purple-accent);">
                    <h4 style="color: var(--purple-accent); margin-bottom: 0.5rem; font-size: 1.2rem;">⚠️ Perhatian</h4>
                    <p style="color: var(--text-light); font-size: 0.95rem;">Cek kuota event secara berkala untuk menghindari overbooking!</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s, transform 0.5s';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>