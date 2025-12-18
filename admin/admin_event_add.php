<?php 
require_once '../config/db_connect.php';
check_admin_login();

$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event = clean_input($_POST['nama_event']);
    $tanggal = clean_input($_POST['tanggal']);
    $waktu = clean_input($_POST['waktu']);
    $lokasi = clean_input($_POST['lokasi']);
    $narasumber = clean_input($_POST['narasumber']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $kuota = intval($_POST['kuota']);

    // Validasi
    if (empty($nama_event) || empty($tanggal) || empty($waktu) || empty($lokasi) || $kuota <= 0) {
        $error = 'Semua field wajib harus diisi dengan benar';
    } else {
        $query = "INSERT INTO event (nama_event, tanggal, waktu, lokasi, narasumber, deskripsi, kuota) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $nama_event, $tanggal, $waktu, $lokasi, $narasumber, $deskripsi, $kuota);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Event berhasil ditambahkan!';
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = 'Gagal menambahkan event';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Event - Admin</title>
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
                <li><a href="admin_event_add.php" class="active">➕ Tambah Event</a></li>
                <li><a href="admin_verifikasi.php">✅ Verifikasi Pendaftaran</a></li>
                <li><a href="../process/process_logout.php" style="color: var(--danger-color);">🚪 Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">Tambah Event Baru</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-container" style="max-width: 800px; margin: 0;">
                <form method="POST" id="event-form">
                    <div class="form-group">
                        <label for="nama_event">Nama Event <span style="color: red;">*</span></label>
                        <input type="text" id="nama_event" name="nama_event" class="form-control" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="tanggal">Tanggal <span style="color: red;">*</span></label>
                            <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="waktu">Waktu <span style="color: red;">*</span></label>
                            <input type="time" id="waktu" name="waktu" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lokasi">Lokasi <span style="color: red;">*</span></label>
                        <input type="text" id="lokasi" name="lokasi" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="narasumber">Narasumber</label>
                        <input type="text" id="narasumber" name="narasumber" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="kuota">Kuota Peserta <span style="color: red;">*</span></label>
                        <input type="number" id="kuota" name="kuota" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Event</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="5"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan Event</button>
                        <a href="admin_dashboard.php" class="btn btn-secondary" style="margin-left: 0.5rem;">Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('event-form').addEventListener('submit', function(e) {
            const kuota = parseInt(document.getElementById('kuota').value);
            if (kuota < 1) {
                e.preventDefault();
                alert('Kuota peserta minimal 1 orang');
            }
        });
    </script>
</body>
</html>