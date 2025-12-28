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
        .event-detail-card {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 2rem 0;
        }
        .event-detail-header {
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .event-detail-header h2 {
            color: var(--dark);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .event-detail-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
        }
        .meta-item {
            display: flex;
            align-items: start;
            gap: 0.75rem;
        }
        .meta-icon {
            font-size: 1.5rem;
        }
        .meta-content h4 {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        .meta-content p {
            font-size: 1.1rem;
            color: var(--dark);
            font-weight: 600;
        }
        .quota-bar {
            margin: 2rem 0;
        }
        .quota-progress {
            height: 30px;
            background: var(--light);
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }
        .quota-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
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
        <div class="event-detail-card">
            <div class="event-detail-header">
                <h2><?php echo htmlspecialchars($event['nama_event']); ?></h2>
                <p style="color: #6b7280; font-size: 1.1rem;">
                    <?php echo htmlspecialchars($event['narasumber'] ?: 'Narasumber akan diumumkan'); ?>
                </p>
            </div>

            <div class="event-detail-meta">
                <div class="meta-item">
                    <span class="meta-icon">📅</span>
                    <div class="meta-content">
                        <h4>Tanggal</h4>
                        <p><?php echo date('d F Y', strtotime($event['tanggal'])); ?></p>
                    </div>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">⏰</span>
                    <div class="meta-content">
                        <h4>Waktu</h4>
                        <p><?php echo date('H:i', strtotime($event['waktu'])); ?> WIB</p>
                    </div>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">📍</span>
                    <div class="meta-content">
                        <h4>Lokasi</h4>
                        <p><?php echo htmlspecialchars($event['lokasi']); ?></p>
                    </div>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">👥</span>
                    <div class="meta-content">
                        <h4>Kuota</h4>
                        <p><?php echo $event['kuota']; ?> peserta</p>
                    </div>
                </div>
            </div>

            <div class="quota-bar">
                <h4 style="margin-bottom: 0.5rem;">Status Pendaftaran</h4>
                <div class="quota-progress">
                    <div class="quota-fill" style="width: <?php echo ($event['jumlah_pendaftar'] / $event['kuota']) * 100; ?>%">
                        <?php echo $event['jumlah_pendaftar']; ?> / <?php echo $event['kuota']; ?>
                    </div>
                </div>
                <p style="margin-top: 0.5rem; color: <?php echo $sisa_kuota > 0 ? 'var(--success-color)' : 'var(--danger-color)'; ?>; font-weight: 600;">
                    <?php if ($sisa_kuota > 0): ?>
                        ✓ Tersisa <?php echo $sisa_kuota; ?> tempat
                    <?php else: ?>
                        ✗ Kuota sudah penuh
                    <?php endif; ?>
                </p>
            </div>

            <div style="margin: 2rem 0;">
                <h3 style="margin-bottom: 1rem;">Deskripsi Event</h3>
                <p style="line-height: 1.8; color: var(--text-dark);">
                    <?php echo nl2br(htmlspecialchars($event['deskripsi'] ?: 'Tidak ada deskripsi untuk event ini.')); ?>
                </p>
            </div>

            <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--border-color);">
                <?php if ($sisa_kuota > 0): ?>
                    <a href="#" id="btn-daftar" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 3rem;">
                        📝 Daftar Sekarang
                    </a>
                <?php else: ?>
                    <button class="btn btn-disabled" style="font-size: 1.1rem; padding: 1rem 3rem;" disabled>
                        Kuota Penuh
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Pendaftaran (Hidden by default) -->
        <div id="form-daftar" class="form-container" style="display: none;">
            <h3 style="text-align: center; margin-bottom: 1.5rem;">Form Pendaftaran Event</h3>
            
            <div id="alert-container"></div>

            <form id="pendaftaran-form">
                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                
                <div class="form-group">
                    <label for="nama">Nama Lengkap <span style="color: var(--danger-color);">*</span></label>
                    <input type="text" id="nama" name="nama" class="form-control" required>
                    <span class="error-message" id="error-nama"></span>
                </div>

                <div class="form-group">
                    <label for="nim">NIM <span style="color: var(--danger-color);">*</span></label>
                    <input type="text" id="nim" name="nim" class="form-control" required>
                    <span class="error-message" id="error-nim"></span>
                </div>

                <div class="form-group">
                    <label for="email">Email <span style="color: var(--danger-color);">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" required>
                    <span class="error-message" id="error-email"></span>
                </div>

                <div class="form-group">
                    <label for="no_hp">No. HP <span style="color: var(--danger-color);">*</span></label>
                    <input type="tel" id="no_hp" name="no_hp" class="form-control" required>
                    <span class="error-message" id="error-no_hp"></span>
                </div>

                <div class="form-group">
                    <label for="jurusan">Jurusan</label>
                    <input type="text" id="jurusan" name="jurusan" class="form-control">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
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