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
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --bg: #f6f7fb;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --card: #ffffff;
            --shadow: 0 10px 25px rgba(17,24,39,0.08);
            --shadow-hover: 0 18px 45px rgba(17,24,39,0.12);
            --radius: 16px;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        /* NAVBAR (SAMA KAYAK DAFTAR EVENT) */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.85);
            border-bottom: 1px solid rgba(229,231,235,0.7);
        }

        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 1rem;
            gap: 1rem;
        }

        .nav-brand h1 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 0.6rem;
            padding: 0;
            margin: 0;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-menu a {
            text-decoration: none;
            padding: 0.55rem 0.95rem;
            border-radius: 999px;
            color: #374151;
            font-weight: 700;
            transition: 0.25s;
            border: 1px solid transparent;
        }

        .nav-menu a:hover {
            background: rgba(102,126,234,0.12);
            border-color: rgba(102,126,234,0.25);
            transform: translateY(-1px);
        }

        .nav-menu a.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 10px 20px rgba(102,126,234,0.25);
        }

        /* HEADER */
        .page-header {
            margin-top: 1.5rem;
            background: linear-gradient(135deg, rgba(102,126,234,0.12), rgba(118,75,162,0.12));
            border: 1px solid rgba(102,126,234,0.18);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: 0 12px 30px rgba(17,24,39,0.05);
        }

        .page-header h2 {
            margin: 0;
            font-size: 1.6rem;
            letter-spacing: 0.2px;
            font-weight: 900;
        }

        .page-header p {
            margin-top: 0.5rem;
            margin-bottom: 0;
            color: var(--muted);
            font-size: 1rem;
            font-weight: 600;
        }

        /* SEARCH CARD */
        .check-wrapper {
            max-width: 850px;
            margin: 1.5rem auto 0;
            display: grid;
            gap: 1.5rem;
        }

        .check-card {
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(229,231,235,0.85);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .check-card h3 {
            margin: 0 0 0.4rem 0;
            font-size: 1.15rem;
            font-weight: 900;
        }

        .check-card p {
            margin: 0 0 1rem 0;
            color: var(--muted);
            font-weight: 600;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap::before {
            content: "🔎";
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.85;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 0.95rem 1rem 0.95rem 2.7rem;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #fff;
            transition: 0.25s;
            font-size: 1rem;
            box-shadow: 0 10px 20px rgba(17,24,39,0.04);
        }

        .form-control:focus {
            outline: none;
            border-color: rgba(102,126,234,0.7);
            box-shadow: 0 0 0 4px rgba(102,126,234,0.15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-weight: 900;
            padding: 0.95rem 1.1rem;
            border-radius: 16px;
            width: 100%;
            transition: 0.25s;
            font-size: 1.02rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 16px 28px rgba(102,126,234,0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 38px rgba(102,126,234,0.35);
        }

        /* STATUS CARD */
        .status-card {
            background: var(--card);
            border-radius: var(--radius);
            padding: 1.6rem;
            box-shadow: var(--shadow-hover);
            border: 1px solid rgba(229,231,235,0.9);
            position: relative;
            overflow: hidden;
        }

        .status-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(102,126,234,0.12), rgba(118,75,162,0.12));
            pointer-events: none;
        }

        .status-inner {
            position: relative;
            z-index: 2;
        }

        .status-header {
            text-align: center;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(229,231,235,0.85);
        }

        .status-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 900;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem 1.2rem;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 900;
            text-transform: uppercase;
            margin: 0.9rem 0 0.2rem;
            border: 1px solid rgba(255,255,255,0.5);
            letter-spacing: 0.5px;
        }

        .badge-pending {
            background: rgba(245,158,11,0.15);
            color: #92400e;
            border-color: rgba(245,158,11,0.25);
            box-shadow: 0 10px 20px rgba(245,158,11,0.18);
        }

        .badge-diterima {
            background: rgba(16,185,129,0.15);
            color: #065f46;
            border-color: rgba(16,185,129,0.25);
            box-shadow: 0 10px 20px rgba(16,185,129,0.18);
        }

        .badge-ditolak {
            background: rgba(239,68,68,0.15);
            color: #991b1b;
            border-color: rgba(239,68,68,0.25);
            box-shadow: 0 10px 20px rgba(239,68,68,0.18);
        }

        /* INFO GRID */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.9rem;
            margin-top: 1rem;
        }

        .info-item {
            background: rgba(255,255,255,0.85);
            border: 1px solid rgba(229,231,235,0.85);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            box-shadow: 0 10px 20px rgba(17,24,39,0.04);
        }

        .info-item.full {
            grid-column: 1 / -1;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--muted);
            font-weight: 800;
            margin-bottom: 0.2rem;
        }

        .info-value {
            font-size: 0.98rem;
            color: var(--text);
            font-weight: 800;
            word-break: break-word;
        }

        /* ALERT INFO */
        .result-alert {
            margin-top: 1.2rem;
            padding: 1rem 1.1rem;
            border-radius: 16px;
            border: 1px solid rgba(229,231,235,0.9);
            background: rgba(255,255,255,0.92);
            box-shadow: 0 12px 26px rgba(17,24,39,0.06);
            font-weight: 700;
            line-height: 1.5;
        }

        .result-alert.info {
            border-color: rgba(102,126,234,0.25);
            background: rgba(102,126,234,0.10);
            color: #1f3b8a;
        }

        .result-alert.success {
            border-color: rgba(16,185,129,0.25);
            background: rgba(16,185,129,0.12);
            color: #065f46;
        }

        .result-alert.danger {
            border-color: rgba(239,68,68,0.25);
            background: rgba(239,68,68,0.12);
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .navbar .container {
                flex-direction: column;
                align-items: flex-start;
            }
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

        <div class="check-wrapper">

            <!-- FORM -->
            <div class="check-card">
                <h3>🔎 Cari Status Pendaftaran</h3>
                <p>Kode akan kamu dapat setelah berhasil daftar event.</p>

                <form method="GET" action="">
                    <div class="form-group input-wrap">
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

                    <div class="form-group" style="margin-top: 0.9rem;">
                        <button type="submit" class="btn btn-primary">
                            ✅ Cek Status
                        </button>
                    </div>
                </form>
            </div>

            <!-- RESULT -->
            <?php if (!empty($kode)): ?>
                <?php if ($pendaftaran): ?>
                    <div class="status-card">
                        <div class="status-inner">

                            <div class="status-header">
                                <h3>Status Pendaftaran</h3>

                                <?php if ($pendaftaran['status'] === 'pending'): ?>
                                    <div class="status-badge badge-pending">⏳ Pending</div>
                                <?php elseif ($pendaftaran['status'] === 'diterima'): ?>
                                    <div class="status-badge badge-diterima">✅ Diterima</div>
                                <?php else: ?>
                                    <div class="status-badge badge-ditolak">❌ Ditolak</div>
                                <?php endif; ?>
                            </div>

                            <div class="info-grid">
                                <div class="info-item full">
                                    <div class="info-label">Kode Pendaftaran</div>
                                    <div class="info-value"><?php echo htmlspecialchars($pendaftaran['kode_unik']); ?></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Nama</div>
                                    <div class="info-value"><?php echo htmlspecialchars($pendaftaran['nama']); ?></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">NIM</div>
                                    <div class="info-value"><?php echo htmlspecialchars($pendaftaran['nim']); ?></div>
                                </div>

                                <div class="info-item full">
                                    <div class="info-label">Event</div>
                                    <div class="info-value"><?php echo htmlspecialchars($pendaftaran['nama_event']); ?></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Tanggal Event</div>
                                    <div class="info-value"><?php echo date('d F Y', strtotime($pendaftaran['tanggal'])); ?></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Waktu</div>
                                    <div class="info-value"><?php echo date('H:i', strtotime($pendaftaran['waktu'])); ?> WIB</div>
                                </div>

                                <div class="info-item full">
                                    <div class="info-label">Lokasi</div>
                                    <div class="info-value"><?php echo htmlspecialchars($pendaftaran['lokasi']); ?></div>
                                </div>

                                <div class="info-item full">
                                    <div class="info-label">Tanggal Daftar</div>
                                    <div class="info-value"><?php echo date('d F Y H:i', strtotime($pendaftaran['tanggal_daftar'])); ?></div>
                                </div>
                            </div>

                            <?php if ($pendaftaran['status'] === 'pending'): ?>
                                <div class="result-alert info">
                                    <strong>📌 Info:</strong> Pendaftaran kamu sedang diverifikasi admin. Coba cek lagi nanti ya.
                                </div>
                            <?php elseif ($pendaftaran['status'] === 'diterima'): ?>
                                <div class="result-alert success">
                                    <strong>🎉 Selamat!</strong> Pendaftaran kamu diterima. Simpan kode ini sebagai bukti saat hadir.
                                </div>
                            <?php else: ?>
                                <div class="result-alert danger">
                                    <strong>😔 Maaf!</strong> Pendaftaran kamu ditolak. Hubungi panitia untuk info lebih lanjut.
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php else: ?>
                    <div class="status-card">
                        <div class="status-inner">
                            <div class="result-alert danger" style="margin:0;">
                                <strong>❌ Tidak Ditemukan!</strong> Kode pendaftaran tidak valid atau tidak ada di sistem.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sistem Event Kampus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
