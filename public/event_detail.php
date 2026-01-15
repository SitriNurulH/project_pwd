<?php 
require_once '../config/db_connect.php';


$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id == 0) {
    header("Location: daftar_event.php");
    exit();
}


$query = "SELECT e.*, 
          COUNT(p.daftar_id) as jumlah_pendaftar
          FROM event e
          LEFT JOIN pendaftaran p ON e.event_id = p.event_id 
              AND p.status = 'diterima' 
              AND p.deleted_at IS NULL
          WHERE e.event_id = ? AND e.deleted_at IS NULL
          GROUP BY e.event_id";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: daftar_event.php");
    exit();
}

$event = $result->fetch_assoc();
$sisa_kuota = $event['kuota'] - $event['jumlah_pendaftar'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['nama_event']); ?> - Detail Event</title>
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
        --shadow: 0 12px 30px rgba(17,24,39,0.08);
        --shadow-hover: 0 18px 45px rgba(17,24,39,0.12);
        --radius: 18px;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
    }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }

    /* DETAIL WRAPPER */
    .detail-wrapper {
        margin: 1.5rem 0 2rem;
        display: grid;
        gap: 1.5rem;
    }

    /* HERO CARD */
    .event-hero {
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-hover);
        border: 1px solid rgba(229,231,235,0.9);
        background: #fff;
    }

    .event-hero-top {
        padding: 1.6rem 1.6rem 1.2rem;
        background: linear-gradient(135deg, rgba(102,126,234,0.14), rgba(118,75,162,0.14));
        border-bottom: 1px solid rgba(229,231,235,0.85);
        position: relative;
    }

    .event-hero-top::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top left, rgba(102,126,234,0.18), transparent 55%),
                    radial-gradient(circle at bottom right, rgba(118,75,162,0.18), transparent 55%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .event-title-big {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 900;
        letter-spacing: 0.2px;
        line-height: 1.2;
    }

    .event-speaker {
        margin: 0.55rem 0 0;
        color: var(--muted);
        font-weight: 700;
        font-size: 1.02rem;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.9rem;
        padding: 0.5rem 0.9rem;
        border-radius: 999px;
        border: 1px solid rgba(102,126,234,0.25);
        background: rgba(102,126,234,0.10);
        color: #1f3b8a;
        font-weight: 800;
        font-size: 0.9rem;
    }

    /* META GRID */
    .event-detail-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 0.9rem;
        padding: 1.3rem 1.6rem 1.4rem;
        background: #fff;
    }

    .meta-card {
        background: rgba(255,255,255,0.85);
        border: 1px solid rgba(229,231,235,0.9);
        border-radius: 16px;
        padding: 1rem 1.05rem;
        box-shadow: 0 12px 22px rgba(17,24,39,0.04);
        display: flex;
        gap: 0.8rem;
        align-items: flex-start;
    }

    .meta-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(102,126,234,0.12);
        border: 1px solid rgba(102,126,234,0.20);
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .meta-content h4 {
        margin: 0 0 0.2rem;
        font-size: 0.85rem;
        color: var(--muted);
        font-weight: 900;
        letter-spacing: 0.3px;
    }

    .meta-content p {
        margin: 0;
        font-size: 1.02rem;
        color: var(--text);
        font-weight: 800;
        line-height: 1.25;
        word-break: break-word;
    }

    /* QUOTA SECTION */
    .quota-box {
        padding: 0 1.6rem 1.6rem;
        background: #fff;
    }

    .quota-box h4 {
        margin: 0.2rem 0 0.65rem;
        font-size: 1rem;
        font-weight: 900;
    }

    .quota-progress {
        height: 34px;
        background: rgba(17,24,39,0.06);
        border-radius: 999px;
        overflow: hidden;
        position: relative;
        border: 1px solid rgba(229,231,235,0.9);
    }

    .quota-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 900;
        transition: width 0.5s ease;
        font-size: 0.95rem;
        min-width: 40px;
    }

    .quota-note {
        margin-top: 0.7rem;
        font-weight: 900;
        font-size: 0.95rem;
    }

    .quota-note.ok {
        color: #047857;
    }

    .quota-note.full {
        color: #b91c1c;
    }

    /* DESCRIPTION */
    .desc-card {
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid rgba(229,231,235,0.9);
        box-shadow: var(--shadow);
        padding: 1.6rem;
    }

    .desc-card h3 {
        margin: 0 0 0.8rem;
        font-size: 1.2rem;
        font-weight: 900;
    }

    .desc-card p {
        margin: 0;
        line-height: 1.85;
        color: #374151;
        font-weight: 600;
    }

    /* ACTION */
    .action-card {
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid rgba(229,231,235,0.9);
        box-shadow: var(--shadow);
        padding: 1.2rem 1.6rem 1.6rem;
        text-align: center;
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
        padding: 0.95rem 1.25rem;
        border-radius: 16px;
        width: 100%;
        transition: 0.25s;
        font-size: 1.02rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 16px 28px rgba(102,126,234,0.28);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 38px rgba(102,126,234,0.35);
    }

    .btn-disabled {
        background: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* FORM REGISTER */
    .register-card {
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(229,231,235,0.9);
        box-shadow: var(--shadow-hover);
        border-radius: var(--radius);
        padding: 1.6rem;
    }

    .register-card h3 {
        margin: 0 0 1rem;
        font-size: 1.2rem;
        font-weight: 900;
        text-align: center;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-group label {
        font-weight: 800;
        color: #374151;
        display: block;
        margin-bottom: 0.45rem;
        font-size: 0.92rem;
    }

    .form-control {
        width: 100%;
        border: 1px solid rgba(229,231,235,1);
        border-radius: 14px;
        padding: 0.9rem 0.95rem;
        font-size: 1rem;
        transition: 0.25s;
        background: #fff;
        box-shadow: 0 12px 22px rgba(17,24,39,0.04);
    }

    .form-control:focus {
        outline: none;
        border-color: rgba(102,126,234,0.75);
        box-shadow: 0 0 0 4px rgba(102,126,234,0.15);
    }

    .form-control.error {
        border-color: rgba(239,68,68,0.8);
        box-shadow: 0 0 0 4px rgba(239,68,68,0.12);
    }

    .error-message {
        display: block;
        margin-top: 0.35rem;
        font-size: 0.85rem;
        font-weight: 800;
        color: #b91c1c;
        min-height: 16px;
    }

    /* ALERT */
    .modern-alert {
        border-radius: 16px;
        padding: 1rem 1.1rem;
        font-weight: 700;
        border: 1px solid rgba(229,231,235,0.9);
        box-shadow: 0 14px 26px rgba(17,24,39,0.06);
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .modern-alert.success {
        background: rgba(16,185,129,0.12);
        border-color: rgba(16,185,129,0.25);
        color: #065f46;
    }

    .modern-alert.danger {
        background: rgba(239,68,68,0.12);
        border-color: rgba(239,68,68,0.25);
        color: #991b1b;
    }

    .modern-alert.info {
        background: rgba(102,126,234,0.10);
        border-color: rgba(102,126,234,0.25);
        color: #1f3b8a;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
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
                <li><a href="cek_status.php">Cek Status</a></li>
                <li><a href="../admin/admin_login.php">Admin Login</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="detail-wrapper">

    <!-- HERO DETAIL -->
    <div class="event-hero">
        <div class="event-hero-top">
            <div class="hero-content">
                <h2 class="event-title-big"><?php echo htmlspecialchars($event['nama_event']); ?></h2>
                <p class="event-speaker">👤 <?php echo htmlspecialchars($event['narasumber'] ?: 'Narasumber akan diumumkan'); ?></p>

                <div class="hero-badge">
                     Event Kampus • Detail & Pendaftaran
                </div>
            </div>
        </div>

        <!-- META -->
        <div class="event-detail-meta">
            <div class="meta-card">
                <div class="meta-icon">📅</div>
                <div class="meta-content">
                    <h4>Tanggal</h4>
                    <p><?php echo date('d F Y', strtotime($event['tanggal'])); ?></p>
                </div>
            </div>

            <div class="meta-card">
                <div class="meta-icon">⏰</div>
                <div class="meta-content">
                    <h4>Waktu</h4>
                    <p><?php echo date('H:i', strtotime($event['waktu'])); ?> WIB</p>
                </div>
            </div>

            <div class="meta-card">
                <div class="meta-icon">📍</div>
                <div class="meta-content">
                    <h4>Lokasi</h4>
                    <p><?php echo htmlspecialchars($event['lokasi']); ?></p>
                </div>
            </div>

            <div class="meta-card">
                <div class="meta-icon">👥</div>
                <div class="meta-content">
                    <h4>Kuota</h4>
                    <p><?php echo $event['kuota']; ?> peserta</p>
                </div>
            </div>
        </div>

        <!-- QUOTA -->
        <div class="quota-box">
            <h4>Status Pendaftaran</h4>
            <div class="quota-progress">
                <div class="quota-fill" style="width: <?php echo ($event['jumlah_pendaftar'] / $event['kuota']) * 100; ?>%">
                    <?php echo $event['jumlah_pendaftar']; ?> / <?php echo $event['kuota']; ?>
                </div>
            </div>

            <p class="quota-note <?php echo $sisa_kuota > 0 ? 'ok' : 'full'; ?>">
                <?php if ($sisa_kuota > 0): ?>
                    ✅ Tersisa <?php echo $sisa_kuota; ?> tempat
                <?php else: ?>
                    ❌ Kuota sudah penuh
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- DESC -->
    <div class="desc-card">
        <h3>Deskripsi Event</h3>
        <p>
            <?php echo nl2br(htmlspecialchars($event['deskripsi'] ?: 'Tidak ada deskripsi untuk event ini.')); ?>
        </p>
    </div>

    <!-- ACTION -->
    <div class="action-card">
        <?php if ($sisa_kuota > 0): ?>
            <a href="#" id="btn-daftar" class="btn btn-primary">
                 Daftar Sekarang
            </a>
        <?php else: ?>
            <button class="btn btn-disabled" disabled>
                🚫 Kuota Penuh
            </button>
        <?php endif; ?>
    </div>

</div>

        <!-- Form Pendaftaran (Hidden by default) -->
        <div id="form-daftar" class="register-card" style="display: none;">
    <h3>📝 Form Pendaftaran Event</h3>

    <div id="alert-container"></div>

    <form id="pendaftaran-form">
        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

        <div class="form-grid">
            <div class="form-group">
                <label for="nama">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                <input type="text" id="nama" name="nama" class="form-control" required>
                <span class="error-message" id="error-nama"></span>
            </div>

            <div class="form-group">
                <label for="nim">NIM <span style="color: var(--danger);">*</span></label>
                <input type="text" id="nim" name="nim" class="form-control" required>
                <span class="error-message" id="error-nim"></span>
            </div>

            <div class="form-group">
                <label for="email">Email <span style="color: var(--danger);">*</span></label>
                <input type="email" id="email" name="email" class="form-control" required>
                <span class="error-message" id="error-email"></span>
            </div>

            <div class="form-group">
                <label for="no_hp">No. HP <span style="color: var(--danger);">*</span></label>
                <input type="tel" id="no_hp" name="no_hp" class="form-control" required>
                <span class="error-message" id="error-no_hp"></span>
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="jurusan">Jurusan</label>
                <input type="text" id="jurusan" name="jurusan" class="form-control">
            </div>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">
                 Daftar Event
            </button>
        </div>
    </form>
</div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sistem Event Kampus. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const btnDaftar = document.getElementById('btn-daftar');
        const formDaftar = document.getElementById('form-daftar');
        const form = document.getElementById('pendaftaran-form');
        const alertContainer = document.getElementById('alert-container');

        
        if (btnDaftar) {
            btnDaftar.addEventListener('click', function(e) {
                e.preventDefault();
                formDaftar.style.display = 'block';
                formDaftar.scrollIntoView({ behavior: 'smooth' });
            });
        }

        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('error'));
            alertContainer.innerHTML = '';

            
            const formData = new FormData(form);

            
            fetch('../process/process_daftar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success">
                            <strong>Berhasil!</strong> ${data.message}
                            <br><strong>Kode Pendaftaran:</strong> ${data.kode_unik}
                        </div>
                    `;
                    form.reset();
                    
                    
                    setTimeout(() => {
                        window.location.href = 'cek_status.php?kode=' + data.kode_unik;
                    }, 3000);
                } else {
                    if (data.errors) {
                        
                        for (const [field, error] of Object.entries(data.errors)) {
                            const errorEl = document.getElementById('error-' + field);
                            const inputEl = document.getElementById(field);
                            if (errorEl) errorEl.textContent = error;
                            if (inputEl) inputEl.classList.add('error');
                        }
                    } else {
                        alertContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <strong>Error!</strong> ${data.message}
                            </div>
                        `;
                    }
                }
                alertContainer.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error:', error);
                alertContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error!</strong> Terjadi kesalahan sistem. Silakan coba lagi.
                    </div>
                `;
            });
        });
    </script>
</body>
</html>