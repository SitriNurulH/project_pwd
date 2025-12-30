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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #ff69b4, #ffb6d9, #dda0dd);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            background: white;
            padding: 3rem;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(255, 105, 180, 0.3);
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .login-header h1 {
            color: #ff69b4;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-weight: 800;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .alert {
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border-radius: 15px;
            background: linear-gradient(135deg, #fee2e2, #ffc0cb);
            color: #991b1b;
            border-left: 5px solid #ff6b9d;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .alert strong {
            display: block;
            margin-bottom: 0.25rem;
        }
        
        .form-group {
            margin-bottom: 1.75rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #ff69b4;
            font-size: 1.05rem;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            font-size: 1.05rem;
            border: 3px solid #ffc0cb;
            border-radius: 15px;
            transition: all 0.3s;
            background: #fff0f5;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #ff69b4;
            box-shadow: 0 0 0 4px rgba(255, 105, 180, 0.1);
            transform: translateY(-2px);
            background: white;
        }
        
        .btn {
            display: inline-block;
            padding: 1rem 1.5rem;
            text-decoration: none;
            border-radius: 15px;
            font-weight: 700;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            width: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ff69b4, #ffb6d9);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 105, 180, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #ff1493, #ff69b4);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 105, 180, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(-1px);
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: #ff69b4;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05rem;
            transition: all 0.3s;
        }
        
        .back-link a:hover {
            color: #ff1493;
            text-decoration: underline;
        }
        
        .demo-info {
            margin-top: 2rem;
            padding: 1.5rem;
            border-top: 2px dashed #ffc0cb;
            text-align: center;
            background: linear-gradient(135deg, #fff0f5, #ffe4e9);
            border-radius: 15px;
        }
        
        .demo-info p {
            margin: 0.5rem 0;
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        .demo-info strong {
            color: #ff69b4;
            font-size: 1.05rem;
        }
        
        .demo-info .demo-title {
            color: #ff69b4;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">🔐</div>
            <h1>Admin Login</h1>
            <p>Sistem Event Kampus</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert">
                <strong>⚠️ Error!</strong>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="../process/process_login_admin.php" method="POST" id="login-form">
            <div class="form-group">
                <label for="username">👤 Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control" 
                    placeholder="Masukkan username admin"
                    required 
                    autocomplete="username"
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    🚀 Login Sekarang
                </button>
            </div>
        </form>

        <div class="back-link">
            <a href="../public/index.php">← Kembali ke Halaman Utama</a>
        </div>

        <div class="demo-info">
            <div class="demo-title">🔑 Demo Login untuk Testing</div>
            <p>Username: <strong>admin</strong></p>
            <p>Password: <strong>admin123</strong></p>
            <p style="font-size: 0.85rem; margin-top: 1rem; opacity: 0.7;">
                💡 Gunakan kredensial di atas untuk login pertama kali
            </p>
        </div>
    </div>

    <script>
        // Client-side validation
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (username === '' || password === '') {
                e.preventDefault();
                alert('❌ Username dan password harus diisi!');
                return false;
            }
            
            if (username.length < 3) {
                e.preventDefault();
                alert('❌ Username minimal 3 karakter!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('❌ Password minimal 6 karakter!');
                return false;
            }
        });
        
        // Prevent multiple submissions
        let isSubmitting = false;
        document.getElementById('login-form').addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            
            // Change button text
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '⏳ Sedang Login...';
            btn.style.opacity = '0.7';
        });
    </script>
</body>
</html>