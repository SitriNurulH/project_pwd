<?php 
require_once '../config/db_connect.php';
check_admin_login();

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$keyword  = isset($_GET['q']) ? clean_input($_GET['q']) : "";

// Dropdown event
$query_events = "SELECT event_id, nama_event FROM event WHERE deleted_at IS NULL ORDER BY tanggal DESC";
$result_events = $conn->query($query_events);

// Query pendaftaran
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
              LIMIT 100";
    $result_pendaftaran = $conn->query($query);
}

// handle msg
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error   = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Verifikasi Pendaftaran - Admin</title>
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
            --danger:#ef4444;
            --warning:#f59e0b;
        }

        body{
            background: var(--bg);
            color: var(--text);
            font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
        }

        /* Layout */
        .admin-container{
            display:flex;
            min-height:100vh;
        }

        /* Sidebar */
        .sidebar{
            width:280px;
            background: linear-gradient(180deg, var(--primary), var(--secondary));
            color:#fff;
            padding: 1.8rem 0;
            position: fixed;
            height:100vh;
            overflow-y:auto;
            box-shadow: 0 16px 30px rgba(17,24,39,0.18);
        }

        .sidebar-brand{
            padding: 0 1.5rem;
            margin-bottom: 1.8rem;
        }

        .sidebar-brand h2{
            margin:0;
            font-size:1.35rem;
            font-weight: 950;
            letter-spacing:0.2px;
        }

        .sidebar-brand p{
            margin-top:0.45rem;
            opacity:0.9;
            font-weight:700;
        }

        .sidebar-menu{
            list-style:none;
            padding:0;
            margin:0;
        }

        .sidebar-menu a{
            display:flex;
            align-items:center;
            gap:0.7rem;
            padding: 0.95rem 1.5rem;
            color:#fff;
            text-decoration:none;
            font-weight:850;
            transition: 0.2s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover{
            background: rgba(255,255,255,0.14);
            border-left-color: rgba(255,255,255,0.75);
        }

        .sidebar-menu a.active{
            background: rgba(255,255,255,0.18);
            border-left-color: #fff;
        }

        /* Main */
        .main-content{
            flex:1;
            margin-left:280px;
            padding: 2rem;
        }

        /* Header */
        .page-top{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:1rem;
            margin-bottom: 1.25rem;
        }

        .page-title{
            margin:0;
            font-size: 1.6rem;
            font-weight: 950;
        }

        .page-subtitle{
            margin-top:0.35rem;
            color: var(--muted);
            font-weight: 650;
        }

        .btn-top{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:0.55rem;
            padding: 0.85rem 1.1rem;
            border-radius: 14px;
            border:none;
            cursor:pointer;
            font-weight: 900;
            text-decoration:none;
            transition:0.25s;
            background: rgba(102,126,234,0.12);
            border:1px solid rgba(102,126,234,0.22);
            color:#1f3b8a;
        }

        .btn-top:hover{
            transform: translateY(-2px);
            background: rgba(102,126,234,0.18);
        }

        /* Cards */
        .card{
            background: var(--card);
            border: 1px solid rgba(229,231,235,0.95);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.2rem;
            margin-bottom: 1.2rem;
        }

        /* Alerts */
        .alert{
            border-radius: 14px;
            padding: 1rem 1.1rem;
            font-weight: 800;
        }

        /* Toolbar filter */
        .toolbar{
            display:flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            align-items:end;
        }

        .toolbar .form-group{
            margin:0;
            flex: 1;
            min-width: 220px;
        }

        .toolbar label{
            display:block;
            margin-bottom: 0.35rem;
            font-weight: 900;
            color: var(--text);
            font-size:0.95rem;
        }

        .toolbar .form-control{
            border-radius: 14px;
            padding: 0.85rem 1rem;
            border: 1px solid rgba(229,231,235,0.95);
            background: #fff;
            font-weight: 700;
            transition:0.25s;
        }

        .toolbar .form-control:focus{
            outline:none;
            border-color: rgba(102,126,234,0.65);
            box-shadow: 0 0 0 4px rgba(102,126,234,0.14);
        }

        .btn-primary-ui{
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color:#fff;
            border:none;
            border-radius: 14px;
            padding: 0.9rem 1.1rem;
            font-weight: 950;
            cursor:pointer;
            transition:0.25s;
            box-shadow: 0 16px 30px rgba(102,126,234,0.24);
        }

        .btn-primary-ui:hover{
            transform: translateY(-2px);
            box-shadow: 0 20px 38px rgba(102,126,234,0.30);
        }

        .btn-soft{
            background: rgba(102,126,234,0.12);
            border: 1px solid rgba(102,126,234,0.22);
            border-radius: 14px;
            padding: 0.9rem 1.1rem;
            font-weight: 950;
            cursor:pointer;
            color:#1f3b8a;
            transition:0.25s;
        }

        .btn-soft:hover{ transform: translateY(-2px); background: rgba(102,126,234,0.18); }

        /* Table */
        .table-wrap{
            overflow:auto;
            border-radius: var(--radius);
            border: 1px solid rgba(229,231,235,0.95);
        }

        table{
            width:100%;
            border-collapse: collapse;
            min-width: 980px;
            background:#fff;
        }

        thead{
            background: linear-gradient(135deg, rgba(102,126,234,0.12), rgba(118,75,162,0.12));
        }

        th{
            text-align:left;
            padding: 1rem;
            font-size: 0.95rem;
            font-weight: 950;
            color: var(--text);
            border-bottom: 1px solid rgba(229,231,235,0.95);
            white-space: nowrap;
        }

        td{
            padding: 1rem;
            border-bottom: 1px solid rgba(229,231,235,0.95);
            color:#374151;
            font-weight:700;
            vertical-align: top;
        }

        tbody tr:hover{
            background: rgba(17,24,39,0.02);
        }

        /* Badges */
        .badge{
            display:inline-flex;
            align-items:center;
            gap:0.35rem;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            font-weight: 950;
            font-size: 0.85rem;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .badge-pending{
            background: rgba(245,158,11,0.14);
            border-color: rgba(245,158,11,0.25);
            color: #92400e;
        }

        .badge-success{
            background: rgba(16,185,129,0.14);
            border-color: rgba(16,185,129,0.25);
            color: #065f46;
        }

        .badge-danger{
            background: rgba(239,68,68,0.14);
            border-color: rgba(239,68,68,0.25);
            color: #991b1b;
        }

        /* Action buttons */
        .btn-mini{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:0.35rem;
            padding: 0.55rem 0.75rem;
            font-weight: 950;
            border-radius: 12px;
            border: 1px solid transparent;
            cursor:pointer;
            transition:0.2s;
            font-size: 0.88rem;
            white-space: nowrap;
        }

        .btn-accept{
            background: rgba(16,185,129,0.14);
            border-color: rgba(16,185,129,0.25);
            color: #065f46;
        }

        .btn-reject{
            background: rgba(239,68,68,0.14);
            border-color: rgba(239,68,68,0.25);
            color: #991b1b;
        }

        .btn-reset{
            background: rgba(102,126,234,0.12);
            border-color: rgba(102,126,234,0.22);
            color:#1f3b8a;
        }

        .btn-mini:hover{ transform: translateY(-2px); }

        /* Responsive */
        @media (max-width: 900px){
            .sidebar{
                position:relative;
                width:100%;
                height:auto;
            }
            .main-content{
                margin-left:0;
            }
            .admin-container{
                flex-direction:column;
            }
            table{ min-width: 900px; }
        }
    </style>
</head>

<body>
<div class="admin-container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h2>🛠️ Admin Panel</h2>
            <p><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php">📊 Dashboard</a></li>
            <li><a href="admin_event_add.php">➕ Tambah Event</a></li>
            <li><a href="admin_verifikasi.php" class="active">✅ Verifikasi</a></li>
            <li><a href="../public/index.php" target="_blank">🌐 Lihat Website</a></li>
            <li><a href="../process/process_logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <!-- Main -->
    <main class="main-content">

        <div class="page-top">
            <div>
                <h1 class="page-title">✅ Verifikasi Pendaftaran</h1>
                <p class="page-subtitle">Kelola pendaftaran peserta: terima / tolak / reset status.</p>
            </div>
            <a href="admin_dashboard.php" class="btn-top">⬅️ Kembali Dashboard</a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="card alert alert-success">
                ✅ <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="card alert alert-danger">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Filter Toolbar -->
        <div class="card">
            <form method="GET" class="toolbar">
                <div class="form-group">
                    <label for="event_id">Filter Event</label>
                    <select id="event_id" name="event_id" class="form-control">
                        <option value="0">Semua Event</option>
                        <?php while ($event = $result_events->fetch_assoc()): ?>
                            <option value="<?php echo $event['event_id']; ?>" <?php echo ($event_id == $event['event_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($event['nama_event']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="q">Cari (Nama / NIM / Email / Kode)</label>
                    <input 
                        type="text" 
                        id="q"
                        name="q"
                        class="form-control"
                        placeholder="contoh: Budi / 20012345 / EVT-2025..."
                        value="<?php echo htmlspecialchars($keyword); ?>"
                    >
                </div>

                <button type="submit" class="btn-primary-ui">🔍 Terapkan</button>
                <a href="admin_verifikasi.php" class="btn-soft">♻️ Reset</a>
            </form>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="table-wrap">
                <table id="table-pendaftaran">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Email</th>
                            <th>Event</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pendaftaran->num_rows > 0): ?>
                            <?php while ($row = $result_pendaftaran->fetch_assoc()): ?>

                                <?php
                                // Filter search manual di PHP (biar gampang tanpa ubah query SQL)
                                if (!empty($keyword)) {
                                    $haystack = strtolower(
                                        $row['kode_unik']." ".$row['nama']." ".$row['nim']." ".$row['email']." ".$row['nama_event']
                                    );
                                    if (strpos($haystack, strtolower($keyword)) === false) {
                                        continue;
                                    }
                                }
                                ?>

                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['kode_unik']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_event']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_daftar'])); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <span class="badge badge-pending">⏳ Pending</span>
                                        <?php elseif ($row['status'] === 'diterima'): ?>
                                            <span class="badge badge-success">✅ Diterima</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">❌ Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <button 
                                                onclick="updateStatus(<?php echo $row['daftar_id']; ?>, 'diterima')"
                                                class="btn-mini btn-accept"
                                            >
                                                ✅ Terima
                                            </button>

                                            <button 
                                                onclick="updateStatus(<?php echo $row['daftar_id']; ?>, 'ditolak')"
                                                class="btn-mini btn-reject"
                                            >
                                                ❌ Tolak
                                            </button>
                                        <?php else: ?>
                                            <button 
                                                onclick="updateStatus(<?php echo $row['daftar_id']; ?>, 'pending')"
                                                class="btn-mini btn-reset"
                                            >
                                                ♻️ Reset
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align:center; padding: 2rem; color: var(--muted);">
                                    😔 Tidak ada data pendaftaran.
                                </td>
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
        let textConfirm = 'Yakin ingin mengubah status pendaftaran ini?';

        if (status === 'diterima') textConfirm = '✅ Terima pendaftaran ini?';
        if (status === 'ditolak') textConfirm = '❌ Tolak pendaftaran ini?';
        if (status === 'pending') textConfirm = '♻️ Reset status jadi Pending?';

        if (!confirm(textConfirm)) return;

        fetch('../process/process_verifikasi.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `daftar_id=${daftarId}&status=${status}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Terjadi kesalahan sistem');
        });
    }
</script>

</body>
</html>
