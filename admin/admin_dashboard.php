<?php 
require_once '../config/db_connect.php';
check_admin_login();

$query_total_event = "SELECT COUNT(*) as total FROM event WHERE deleted_at IS NULL";
$total_event = $conn->query($query_total_event)->fetch_assoc()['total'];

$query_total_peserta = "SELECT COUNT(DISTINCT peserta_id) as total FROM pendaftaran WHERE deleted_at IS NULL";
$total_peserta = $conn->query($query_total_peserta)->fetch_assoc()['total'];

$query_pending = "SELECT COUNT(*) as total FROM pendaftaran WHERE status = 'pending' AND deleted_at IS NULL";
$total_pending = $conn->query($query_pending)->fetch_assoc()['total'];

$query_diterima = "SELECT COUNT(*) as total FROM pendaftaran WHERE status = 'diterima' AND deleted_at IS NULL";
$total_diterima = $conn->query($query_diterima)->fetch_assoc()['total'];

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

    <style>
        :root{
            --primary:#667eea;
            --secondary:#764ba2;
            --bg:#f6f7fb;
            --text:#111827;
            --muted:#6b7280;
            --border:#e5e7eb;
            --card:#ffffff;
            --shadow:0 10px 25px rgba(17,24,39,0.08);
            --shadow-hover:0 18px 45px rgba(17,24,39,0.12);
            --radius:18px;
            --success:#10b981;
            --warning:#f59e0b;
            --danger:#ef4444;
        }

        body{
            background: var(--bg);
            color: var(--text);
            font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
            margin:0;
        }

        /* LAYOUT */
        .admin-container{
            display:flex;
            min-height:100vh;
        }

        /* SIDEBAR */
        .sidebar{
            width:280px;
            padding: 1.6rem 1rem;
            background: linear-gradient(180deg, rgba(102,126,234,1), rgba(118,75,162,1));
            color: #fff;
            position: sticky;
            top:0;
            height:100vh;
            box-shadow: 0 18px 45px rgba(17,24,39,0.18);
        }

        .sidebar-brand{
            padding: 0.8rem 0.8rem 1.2rem;
            border-radius: var(--radius);
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.18);
            margin-bottom: 1.1rem;
        }

        .sidebar-brand h2{
            margin:0;
            font-size: 1.2rem;
            font-weight: 900;
            letter-spacing:0.2px;
        }

        .sidebar-brand p{
            margin:0.35rem 0 0;
            font-size: 0.92rem;
            opacity: 0.95;
            font-weight: 600;
        }

        .sidebar-menu{
            list-style:none;
            padding:0;
            margin:0.8rem 0 0;
            display:flex;
            flex-direction:column;
            gap:0.35rem;
        }

        .sidebar-menu a{
            display:flex;
            align-items:center;
            gap:0.65rem;
            padding: 0.85rem 0.9rem;
            border-radius: 14px;
            text-decoration:none;
            color:#fff;
            font-weight: 800;
            transition: 0.25s;
            border: 1px solid transparent;
        }

        .sidebar-menu a:hover{
            background: rgba(255,255,255,0.16);
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        .sidebar-menu a.active{
            background: rgba(255,255,255,0.22);
            border-color: rgba(255,255,255,0.25);
            box-shadow: 0 10px 20px rgba(17,24,39,0.12);
        }

        .sidebar-footer{
            margin-top: 1.3rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.2);
            opacity: 0.95;
            font-size: 0.85rem;
        }

        /* MAIN */
        .main-content{
            flex:1;
            padding: 1.6rem 1.6rem 2rem;
        }

        /* HEADER */
        .admin-header{
            background: var(--card);
            border:1px solid rgba(229,231,235,0.9);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.3rem 1.5rem;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
        }

        .admin-header h1{
            margin:0;
            font-size: 1.4rem;
            font-weight: 950;
        }

        .admin-header p{
            margin: 0.25rem 0 0;
            color: var(--muted);
            font-weight: 650;
        }

        .btn-logout{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:0.55rem;
            padding: 0.85rem 1.05rem;
            border-radius: 14px;
            text-decoration:none;
            font-weight: 900;
            border:none;
            cursor:pointer;
            transition: 0.25s;
            color:#fff;
            background: linear-gradient(135deg, rgba(239,68,68,1), rgba(190,18,60,1));
            box-shadow: 0 16px 28px rgba(239,68,68,0.22);
        }

        .btn-logout:hover{
            transform: translateY(-2px);
            box-shadow: 0 20px 35px rgba(239,68,68,0.32);
        }

        /* ALERTS */
        .alert{
            border-radius: 16px;
            padding: 1rem 1.1rem;
            margin-top: 1rem;
            border: 1px solid rgba(229,231,235,0.9);
            box-shadow: 0 12px 25px rgba(17,24,39,0.06);
            font-weight: 700;
        }

        /* STATS GRID */
        .stats-grid{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .stat-card{
            background: var(--card);
            border:1px solid rgba(229,231,235,0.9);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.2rem 1.2rem;
            display:flex;
            align-items:center;
            gap:0.9rem;
            transition: 0.25s;
        }

        .stat-card:hover{
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(102,126,234,0.22);
        }

        .stat-icon{
            width: 50px;
            height: 50px;
            border-radius: 16px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size: 1.6rem;
            background: rgba(102,126,234,0.14);
            border:1px solid rgba(102,126,234,0.22);
        }

        .stat-info h3{
            margin:0;
            font-size: 1.75rem;
            font-weight: 950;
            letter-spacing:0.2px;
        }

        .stat-info p{
            margin:0.2rem 0 0;
            color: var(--muted);
            font-weight: 700;
            font-size: 0.95rem;
        }

        /* CONTENT CARD */
        .content-card{
            background: var(--card);
            border:1px solid rgba(229,231,235,0.9);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.4rem 1.5rem;
            margin-top: 1rem;
        }

        .content-head{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(229,231,235,0.9);
        }

        .content-head h2{
            margin:0;
            font-size: 1.2rem;
            font-weight: 950;
        }

        .btn-add{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:0.55rem;
            padding: 0.85rem 1.05rem;
            border-radius: 14px;
            text-decoration:none;
            font-weight: 900;
            border:none;
            cursor:pointer;
            transition: 0.25s;
            color:#fff;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 16px 28px rgba(102,126,234,0.22);
        }

        .btn-add:hover{
            transform: translateY(-2px);
            box-shadow: 0 20px 35px rgba(102,126,234,0.34);
        }

        /* TABLE */
        .table-responsive{
            overflow-x:auto;
            border-radius: 16px;
            border:1px solid rgba(229,231,235,0.9);
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:#fff;
        }

        thead th{
            text-align:left;
            padding: 1rem;
            font-size: 0.95rem;
            font-weight: 900;
            background: rgba(102,126,234,0.10);
            border-bottom: 1px solid rgba(229,231,235,0.9);
            position: sticky;
            top: 0;
            z-index: 2;
        }

        tbody td{
            padding: 1rem;
            border-bottom: 1px solid rgba(229,231,235,0.85);
            color:#111827;
            font-weight: 650;
            vertical-align: top;
        }

        tbody tr:hover{
            background: rgba(17,24,39,0.03);
        }

        /* Badge */
        .badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            font-weight: 900;
            font-size: 0.85rem;
            border: 1px solid transparent;
        }

        .badge-success{
            background: rgba(16,185,129,0.14);
            color: #047857;
            border-color: rgba(16,185,129,0.25);
        }

        .badge-warning{
            background: rgba(245,158,11,0.14);
            color: #92400e;
            border-color: rgba(245,158,11,0.25);
        }

        .badge-danger{
            background: rgba(239,68,68,0.14);
            color: #991b1b;
            border-color: rgba(239,68,68,0.25);
        }

        /* ACTION BUTTONS */
        .btn-action{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding: 0.55rem 0.8rem;
            border-radius: 12px;
            text-decoration:none;
            font-weight: 900;
            font-size: 0.88rem;
            border:1px solid rgba(229,231,235,0.9);
            background: rgba(17,24,39,0.03);
            color:#111827;
            transition:0.25s;
            margin: 0 0.2rem;
        }

        .btn-action:hover{
            transform: translateY(-2px);
            background: rgba(17,24,39,0.05);
        }

        .btn-view{
            border-color: rgba(102,126,234,0.25);
            background: rgba(102,126,234,0.12);
            color:#1f3b8a;
        }

        .btn-delete{
            border-color: rgba(239,68,68,0.25);
            background: rgba(239,68,68,0.12);
            color:#991b1b;
        }

        /* QUICK INFO */
        .quick-grid{
            margin-top: 1rem;
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1rem;
        }

        .quick-card{
            background: var(--card);
            border: 1px solid rgba(229,231,235,0.9);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.2rem;
        }

        .quick-card h4{
            margin:0 0 0.4rem;
            font-size: 1rem;
            font-weight: 950;
        }

        .quick-card p{
            margin:0;
            color: var(--muted);
            font-weight: 650;
            line-height:1.6;
        }

        .quick-card.tip{ border-left: 5px solid var(--primary); }
        .quick-card.warn{ border-left: 5px solid var(--warning); }

        /* RESPONSIVE */
        @media (max-width: 900px){
            .admin-container{ flex-direction:column; }
            .sidebar{
                width:100%;
                height:auto;
                position:relative;
                border-radius: 0 0 18px 18px;
            }
            .main-content{ padding: 1.2rem; }
            .content-head{ flex-direction:column; align-items:flex-start; }
        }
    </style>
</head>

<body>
    <div class="admin-container">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>💎 Admin Panel</h2>
                <p><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="admin_event_add.php">➕ Tambah Event</a></li>
                <li><a href="admin_verifikasi.php">✅ Verifikasi Pendaftaran</a></li>
                <li><a href="../public/index.php" target="_blank">🌐 Lihat Website</a></li>
                <li><a href="../process/process_logout.php">🚪 Logout</a></li>
            </ul>

            <div class="sidebar-footer">
                <div>© 2025 Sistem Event Kampus</div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Header -->
            <div class="admin-header">
                <div>
                    <h1>👋 Dashboard Admin</h1>
                    <p>Selamat datang kembali, <strong><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></strong>!</p>
                </div>

                <a href="../process/process_logout.php" class="btn-logout">
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
                    <div class="stat-icon">📅</div>
                    <div class="stat-info">
                        <h3><?php echo $total_event; ?></h3>
                        <p>Total Event</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3><?php echo $total_peserta; ?></h3>
                        <p>Total Peserta</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-info">
                        <h3><?php echo $total_pending; ?></h3>
                        <p>Pending Approval</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <h3><?php echo $total_diterima; ?></h3>
                        <p>Pendaftar Diterima</p>
                    </div>
                </div>
            </div>

            <!-- Event List -->
            <div class="content-card">
                <div class="content-head">
                    <h2>📋 Daftar Event Terbaru</h2>
                    <a href="admin_event_add.php" class="btn-add">➕ Tambah Event Baru</a>
                </div>

                <?php if ($result_events->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 34%;">📌 Nama Event</th>
                                    <th>📅 Tanggal</th>
                                    <th>📍 Lokasi</th>
                                    <th style="text-align:center;">👥 Kuota</th>
                                    <th style="text-align:center;">📊 Pendaftar</th>
                                    <th style="text-align:center;">⚙️ Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($event = $result_events->fetch_assoc()): ?>
                                    <?php 
                                    $percentage = ($event['kuota'] > 0) ? (($event['jumlah_pendaftar'] / $event['kuota']) * 100) : 0;
                                    $status_class = $percentage >= 100 ? 'danger' : ($percentage >= 75 ? 'warning' : 'success');
                                    ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:950; color:#111827;">
                                                <?php echo htmlspecialchars($event['nama_event']); ?>
                                            </div>
                                            <div style="color:var(--muted); font-weight:650; font-size:0.9rem; margin-top:0.15rem;">
                                                👤 <?php echo htmlspecialchars($event['narasumber'] ?: 'Narasumber belum ada'); ?>
                                            </div>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($event['tanggal'])); ?></td>
                                        <td><?php echo htmlspecialchars($event['lokasi']); ?></td>
                                        <td style="text-align:center; font-weight:900;"><?php echo $event['kuota']; ?></td>
                                        <td style="text-align:center;">
                                            <span class="badge badge-<?php echo $status_class; ?>">
                                                <?php echo $event['jumlah_pendaftar']; ?> / <?php echo $event['kuota']; ?>
                                            </span>
                                        </td>
                                        <td style="text-align:center;">
                                            <a class="btn-action btn-view"
                                               href="admin_verifikasi.php?event_id=<?php echo $event['event_id']; ?>">
                                                👁️ Lihat
                                            </a>

                                            <a class="btn-action btn-delete"
                                               href="admin_event_delete.php?id=<?php echo $event['event_id']; ?>"
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
                    <div class="no-data" style="margin-top:1rem;">
                        <p style="font-size: 3rem; margin-bottom: 1rem;">📭</p>
                        <p style="font-size: 1.2rem; font-weight: 900;">Belum ada event</p>
                        <p style="color: var(--muted); margin-top: 0.5rem;">Klik tombol tambah event untuk menambahkan event pertama.</p>
                        <a href="admin_event_add.php" class="btn-add" style="margin-top: 1rem; display:inline-flex;">
                            ➕ Tambah Event Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Info -->
            <div class="quick-grid">
                <div class="quick-card tip">
                    <h4>📌 Tips</h4>
                    <p>Verifikasi pendaftaran lebih cepat supaya peserta langsung dapat konfirmasi.</p>
                </div>
                <div class="quick-card warn">
                    <h4>⚠️ Perhatian</h4>
                    <p>Pantau kuota event secara berkala untuk menghindari overbooking.</p>
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
                alert.style.transform = 'translateY(-12px)';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
