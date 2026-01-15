<?php 
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 Admin Login - Sistem Event Kampus</title>

    <style>
        :root{
            --primary:#667eea;
            --secondary:#764ba2;
            --bg:#f6f7fb;
            --text:#111827;
            --muted:#6b7280;
            --border:#e5e7eb;
            --card:#ffffff;
            --shadow:0 16px 45px rgba(17,24,39,0.12);
            --radius:22px;
            --danger:#ef4444;
            --success:#10b981;
        }

        *{ margin:0; padding:0; box-sizing:border-box; }

        body{
            font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background: radial-gradient(circle at top left, rgba(102,126,234,0.25), transparent 55%),
                        radial-gradient(circle at bottom right, rgba(118,75,162,0.25), transparent 55%),
                        var(--bg);
            padding: 1.2rem;
        }

        .login-wrap{
            width:100%;
            max-width: 460px;
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229,231,235,0.9);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow:hidden;
        }

        .login-top{
            padding: 2rem 2rem 1.4rem;
            background: linear-gradient(135deg, rgba(102,126,234,0.14), rgba(118,75,162,0.14));
            position: relative;
        }

        .login-top::after{
            content:"";
            position:absolute;
            inset:0;
            background:
                radial-gradient(circle at top left, rgba(102,126,234,0.18), transparent 55%),
                radial-gradient(circle at bottom right, rgba(118,75,162,0.18), transparent 55%);
            pointer-events:none;
        }

        .brand{
            position:relative;
            z-index:2;
            display:flex;
            align-items:center;
            gap:0.9rem;
        }

        .brand-icon{
            width:54px;
            height:54px;
            border-radius: 18px;
            display:flex;
            align-items:center;
            justify-content:center;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color:#fff;
            font-size: 1.6rem;
            box-shadow: 0 16px 28px rgba(102,126,234,0.25);
        }

        .brand h1{
            font-size: 1.55rem;
            font-weight: 950;
            letter-spacing:0.2px;
            color: var(--text);
        }

        .brand p{
            margin-top: 0.2rem;
            color: var(--muted);
            font-weight: 650;
            font-size: 0.98rem;
        }

        .login-body{
            padding: 1.6rem 2rem 2rem;
            background: rgba(255,255,255,0.9);
        }

        .alert{
            border-radius: 16px;
            padding: 1rem 1.1rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(239,68,68,0.25);
            background: rgba(239,68,68,0.10);
            color: #991b1b;
            font-weight: 700;
        }

        .form-group{
            margin-bottom: 1rem;
        }

        label{
            display:block;
            font-weight: 900;
            margin-bottom: 0.45rem;
            color: var(--text);
            font-size: 0.95rem;
        }

        .input{
            width:100%;
            padding: 0.95rem 1rem;
            border-radius: 16px;
            border: 1px solid rgba(229,231,235,0.95);
            outline:none;
            background: #fff;
            font-weight: 650;
            transition: 0.25s;
        }

        .input:focus{
            border-color: rgba(102,126,234,0.65);
            box-shadow: 0 0 0 4px rgba(102,126,234,0.14);
        }

        .btn{
            width:100%;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:0.55rem;
            padding: 0.95rem 1rem;
            border-radius: 16px;
            border:none;
            cursor:pointer;
            font-weight: 950;
            font-size: 1.05rem;
            transition: 0.25s;
        }

        .btn-primary{
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color:#fff;
            box-shadow: 0 16px 28px rgba(102,126,234,0.25);
        }

        .btn-primary:hover{
            transform: translateY(-2px);
            box-shadow: 0 20px 36px rgba(102,126,234,0.32);
        }

        .back-link{
            text-align:center;
            margin-top: 1rem;
        }

        .back-link a{
            color:#1f3b8a;
            font-weight: 850;
            text-decoration:none;
        }

        .back-link a:hover{
            text-decoration: underline;
        }

        .demo-box{
            margin-top: 1.3rem;
            padding: 1.1rem;
            border-radius: 18px;
            border: 1px dashed rgba(102,126,234,0.45);
            background: rgba(102,126,234,0.06);
        }

        .demo-box h3{
            margin:0 0 0.55rem;
            font-size: 1rem;
            font-weight: 950;
            color:#1f3b8a;
        }

        .demo-box p{
            margin:0.25rem 0;
            color: var(--muted);
            font-weight: 650;
        }

        .demo-box strong{
            color: var(--text);
        }

        .mini{
            margin-top: 0.7rem;
            font-size: 0.85rem;
            opacity:0.75;
        }
    </style>
</head>
<body>

    <div class="login-wrap">
        <div class="login-top">
            <div class="brand">
                <div class="brand-icon">🔐</div>
                <div>
                    <h1>Admin Login</h1>
                    <p>Sistem Event Kampus</p>
                </div>
            </div>
        </div>

        <div class="login-body">

            <?php if (!empty($error)): ?>
                <div class="alert">
                    <strong>⚠️ Login Gagal</strong><br>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="../process/process_login_admin.php" method="POST" id="login-form" autocomplete="off">
                <div class="form-group">
                    <label for="username">👤 Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="input" 
                        placeholder="Masukkan username admin"
                        required 
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">🔒 Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="input" 
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary" id="btn-login">
                    🚀 Login Sekarang
                </button>
            </form>

            <div class="back-link">
                <a href="../public/index.php">← Kembali ke Halaman Utama</a>
            </div>

            <div class="demo-box">
                <h3>🔑 Demo Login (Testing)</h3>
                <p>Username: <strong>admin</strong></p>
                <p>Password: <strong>admin123</strong></p>
                <p class="mini">💡 Gunakan kredensial ini untuk login pertama kali.</p>
            </div>

        </div>
    </div>

    <script>
        // Basic validation + prevent multi submit
        let isSubmitting = false;
        document.getElementById('login-form').addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            const u = document.getElementById('username').value.trim();
            const p = document.getElementById('password').value.trim();

            if (!u || !p) {
                e.preventDefault();
                alert('❌ Username dan password wajib diisi!');
                return false;
            }

            if (u.length < 3) {
                e.preventDefault();
                alert('❌ Username minimal 3 karakter!');
                return false;
            }

            if (p.length < 6) {
                e.preventDefault();
                alert('❌ Password minimal 6 karakter!');
                return false;
            }

            isSubmitting = true;

            const btn = document.getElementById('btn-login');
            btn.innerHTML = '⏳ Sedang Login...';
            btn.style.opacity = '0.8';
        });
    </script>

</body>
</html>
