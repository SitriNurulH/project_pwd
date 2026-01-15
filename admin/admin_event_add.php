<?php 
require_once '../config/db_connect.php';
check_admin_login();

$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event  = clean_input($_POST['nama_event']);
    $tanggal     = clean_input($_POST['tanggal']);
    $waktu       = clean_input($_POST['waktu']);
    $lokasi      = clean_input($_POST['lokasi']);
    $narasumber  = clean_input($_POST['narasumber']);
    $deskripsi   = clean_input($_POST['deskripsi']);
    $kuota       = intval($_POST['kuota']);

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
    <title>➕ Tambah Event - Admin</title>
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

        /* TOP HEADER */
        .page-head{
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

        .page-head h1{
            margin:0;
            font-size: 1.35rem;
            font-weight: 950;
        }

        .page-head p{
            margin:0.25rem 0 0;
            color: var(--muted);
            font-weight: 650;
        }

        .head-actions{
            display:flex;
            gap:0.6rem;
            flex-wrap:wrap;
        }

        .btn-ui{
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
        }

        .btn-primary-ui{
            color:#fff;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 16px 28px rgba(102,126,234,0.22);
        }

        .btn-primary-ui:hover{
            transform: translateY(-2px);
            box-shadow: 0 20px 35px rgba(102,126,234,0.34);
        }

        .btn-outline-ui{
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(102,126,234,0.25);
            color:#1f3b8a;
            box-shadow: 0 12px 22px rgba(17,24,39,0.06);
        }

        .btn-outline-ui:hover{
            transform: translateY(-2px);
            background:#fff;
        }

        /* CARD FORM */
        .form-card{
            margin-top: 1rem;
            background: var(--card);
            border:1px solid rgba(229,231,235,0.9);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.4rem 1.5rem;
        }

        .form-title{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(229,231,235,0.9);
        }

        .form-title h2{
            margin:0;
            font-size: 1.15rem;
            font-weight: 950;
        }

        .hint{
            margin:0;
            color: var(--muted);
            font-weight: 650;
            font-size: 0.92rem;
        }

        /* Inputs */
        .grid-2{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group{
            margin-bottom: 1rem;
        }

        label{
            display:block;
            font-weight: 900;
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
            color:#111827;
        }

        .required{
            color: var(--danger);
        }

        .input{
            width:100%;
            padding: 0.95rem 1rem;
            border: 1px solid rgba(229,231,235,0.95);
            border-radius: 14px;
            outline: none;
            font-weight: 650;
            transition: 0.25s;
            background: rgba(255,255,255,0.9);
        }

        .input:focus{
            border-color: rgba(102,126,234,0.65);
            box-shadow: 0 0 0 4px rgba(102,126,234,0.14);
            background:#fff;
        }

        textarea.input{
            min-height: 130px;
            resize: vertical;
        }

        .form-actions{
            display:flex;
            gap:0.6rem;
            flex-wrap:wrap;
            margin-top: 0.6rem;
        }

        .btn-save{
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color:#fff;
            box-shadow: 0 16px 28px rgba(102,126,234,0.22);
        }

        .btn-save:hover{
            transform: translateY(-2px);
            box-shadow: 0 20px 35px rgba(102,126,234,0.34);
        }

        .btn-cancel{
            background: rgba(17,24,39,0.04);
            border: 1px solid rgba(229,231,235,0.95);
            color:#111827;
        }

        .btn-cancel:hover{
            transform: translateY(-2px);
            background: rgba(17,24,39,0.06);
        }

        /* ALERT */
        .alert{
            border-radius: 16px;
            padding: 1rem 1.1rem;
            margin-top: 1rem;
            border: 1px solid rgba(229,231,235,0.9);
            box-shadow: 0 12px 25px rgba(17,24,39,0.06);
            font-weight: 700;
        }

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
        }

        @media (max-width: 768px){
            .grid-2{ grid-template-columns: 1fr; }
            .page-head{ flex-direction:column; align-items:flex-start; }
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
                <li><a href="admin_dashboard.php">📊 Dashboard</a></li>
                <li><a href="admin_event_add.php" class="active">➕ Tambah Event</a></li>
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
            <div class="page-head">
                <div>
                    <h1>➕ Tambah Event Baru</h1>
                    <p>Isi form berikut untuk membuat event baru di sistem.</p>
                </div>

                <div class="head-actions">
                    <a href="admin_dashboard.php" class="btn-ui btn-outline-ui">⬅️ Kembali</a>
                    <a href="../process/process_logout.php" class="btn-ui btn-primary-ui">🚪 Logout</a>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <strong>❌ Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <strong>✅ Berhasil:</strong> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Form Card -->
            <div class="form-card">
                <div class="form-title">
                    <div>
                        <h2>📌 Detail Event</h2>
                        <p class="hint">Field bertanda <span class="required">*</span> wajib diisi.</p>
                    </div>
                </div>

                <form method="POST" id="event-form" autocomplete="off">
                    <div class="form-group">
                        <label for="nama_event">Nama Event <span class="required">*</span></label>
                        <input type="text" id="nama_event" name="nama_event" class="input" placeholder="Contoh: Seminar Teknologi AI" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label for="tanggal">Tanggal <span class="required">*</span></label>
                            <input type="date" id="tanggal" name="tanggal" class="input" required>
                        </div>

                        <div class="form-group">
                            <label for="waktu">Waktu <span class="required">*</span></label>
                            <input type="time" id="waktu" name="waktu" class="input" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label for="lokasi">Lokasi <span class="required">*</span></label>
                            <input type="text" id="lokasi" name="lokasi" class="input" placeholder="Contoh: Auditorium Utama" required>
                        </div>

                        <div class="form-group">
                            <label for="kuota">Kuota Peserta <span class="required">*</span></label>
                            <input type="number" id="kuota" name="kuota" class="input" min="1" placeholder="Contoh: 100" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="narasumber">Narasumber</label>
                        <input type="text" id="narasumber" name="narasumber" class="input" placeholder="Contoh: Prof. Ani Wijaya">
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Event</label>
                        <textarea id="deskripsi" name="deskripsi" class="input" placeholder="Tuliskan deskripsi singkat event..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-ui btn-save">💾 Simpan Event</button>
                        <a href="admin_dashboard.php" class="btn-ui btn-cancel">✖️ Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('event-form').addEventListener('submit', function(e) {
            const kuota = parseInt(document.getElementById('kuota').value);
            if (isNaN(kuota) || kuota < 1) {
                e.preventDefault();
                alert('Kuota peserta minimal 1 orang');
            }
        });

        // Auto-hide alert (optional)
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(a => {
                a.style.transition = 'opacity .4s, transform .4s';
                a.style.opacity = '0';
                a.style.transform = 'translateY(-10px)';
                setTimeout(() => a.remove(), 450);
            });
        }, 5000);
    </script>
</body>
</html>
