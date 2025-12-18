<?php 
require_once '../config/db_connect.php';

$kode = isset($_GET['kode']) ? clean_input($_GET['kode']) : '';
$pendaftaran = null;

if (!empty($kode)) {
    $query = "SELECT pf.*, ps.nama, ps.nim, ps.email, ps.no_hp, e.nama_event, e.tanggal, e.waktu, e.lokasi 
              FROM pendaftaran pf
              JOIN peserta ps ON pf.peserta_id = ps.peserta_id
              JOIN event e ON pf.event_id = e.event_id
              WHERE pf.kode_unik = ? AND pf.deleted_at IS NULL";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $pendaftaran = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pendaftaran - Sistem Event Kampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .status-card {
            max-width: 600px;
            margin: 2rem auto;
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .status-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }
        .status-badge-large {
            display: inline-block;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 700;
            text-transform: uppercase;
            margin: 1rem 0;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-diterima { background: #d1fae5; color: #065f46; }
        .status-ditolak { background: #fee2e2; color: #991b1b; }
        .info-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .info-label {
            width: 40%;
            font-weight: 600;
            color: #6b7280;
        }
        .info-value {
            width: 60%;
            color: var(--dark);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>📅 Event Kampus</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="daftar_event.php">Daftar Event</a></li>
                <li><a href="cek_status.php" class="active">Cek Status</a></li>
                <li><a href="../admin/admin_login.php">Admin Login</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <section class="page-header">
            <h2>Cek Status Pendaftaran</h2>
            <p>Masukkan kode pendaftaran untuk melihat status</p>
        </section>

        <div class="form-container" style="max-width: 500px;">
            <form method="GET" action="">
                <div class="form-group">
                    <label for="kode">Kode Pendaftaran</label>
                    <input 
                        type="text" 
                        id="kode" 
                        name="kode" 
                        class="form-control" 
                        placeholder="Contoh: EVT-2025-ABC123"
                        value="<?php echo htmlspecialchars($kode); ?>"
                        required
                    >
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Cek Status
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($kode)): ?>
            <?php if ($pendaftaran): ?>
                <div class="status-card">
                    <div class="status-header">
                        <h3>Status Pendaftaran</h3>
                        <div class="status-badge-large status-<?php echo $pendaftaran['status']; ?>">
                            <?php 
                            if ($pendaftaran['status'] === 'pending') echo '⏳ Menunggu Verifikasi';
                            elseif ($pendaftaran['status'] === 'diterima') echo '✅ Diterima';
                            else echo '❌ Ditolak';
                            ?>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Kode Pendaftaran:</div>
                        <div class="info-value"><strong><?php echo htmlspecialchars($pendaftaran['kode_unik']); ?></strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nama:</div>
                        <div class="info-value"><?php echo htmlspecialchars($pendaftaran['nama']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">NIM:</div>
                        <div class="info-value"><?php echo htmlspecialchars($pendaftaran['nim']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Event:</div>
                        <div class="info-value"><strong><?php echo htmlspecialchars($pendaftaran['nama_event']); ?></strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Event:</div>
                        <div class="info-value"><?php echo date('d F Y', strtotime($pendaftaran['tanggal'])); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Waktu:</div>
                        <div class="info-value"><?php echo date('H:i', strtotime($pendaftaran['waktu'])); ?> WIB</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Lokasi:</div>
                        <div class="info-value"><?php echo htmlspecialchars($pendaftaran['lokasi']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Daftar:</div>
                        <div class="info-value"><?php echo date('d F Y H:i', strtotime($pendaftaran['tanggal_daftar'])); ?></div>
                    </div>

                    <?php if ($pendaftaran['status'] === 'pending'): ?>
                        <div class="alert alert-info" style="margin-top: 2rem;">
                            <strong>📌 Info:</strong> Pendaftaran Anda sedang dalam proses verifikasi oleh admin. Silakan cek kembali secara berkala.
                        </div>
                    <?php elseif ($pendaftaran['status'] === 'diterima'): ?>
                        <div class="alert alert-success" style="margin-top: 2rem;">
                            <strong>🎉 Selamat!</strong> Pendaftaran Anda telah diterima. Jangan lupa hadir pada event tersebut. Simpan kode pendaftaran ini sebagai bukti.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger" style="margin-top: 2rem;">
                            <strong> Maaf!</strong> Pendaftaran Anda ditolak. Silakan hubungi panitia untuk informasi lebih lanjut.
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-danger" style="max-width: 500px; margin: 2rem auto;">
                    <strong> Tidak Ditemukan!</strong> Kode pendaftaran tidak valid atau tidak ditemukan dalam sistem.
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sistem Event Kampus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>