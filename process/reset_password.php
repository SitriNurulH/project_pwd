<?php


require_once 'config/db_connect.php';


$new_password = "admin123";
$username = "admin";
$nama_lengkap = "Administrator";

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reset Password Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
        }
        h2 { color: #10b981; margin-bottom: 1rem; }
        h2.error { color: #ef4444; }
        .info-box {
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            border-left: 4px solid #2563eb;
        }
        .info-box p {
            margin: 0.5rem 0;
            color: #374151;
        }
        .info-box strong {
            color: #1f2937;
        }
        .warning {
            background: #fee2e2;
            border-left-color: #ef4444;
            color: #991b1b;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 8px;
        }
        .success {
            background: #d1fae5;
            border-left-color: #10b981;
            color: #065f46;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 8px;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        code {
            background: #1f2937;
            color: #10b981;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: monospace;
        }
        hr {
            margin: 1.5rem 0;
            border: none;
            border-top: 2px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class='container'>";


$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);


$check_query = "SELECT admin_id FROM admin WHERE username = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    
    $query = "UPDATE admin SET password = ?, nama_lengkap = ?, deleted_at = NULL WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $hashed_password, $nama_lengkap, $username);
    $action = "diupdate";
} else {
    
    $query = "INSERT INTO admin (username, password, nama_lengkap) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $hashed_password, $nama_lengkap);
    $action = "ditambahkan";
}

if ($stmt->execute()) {
    echo "<h2> Password Berhasil " . ucfirst($action) . "!</h2>";
    
    echo "<div class='info-box'>
        <p><strong>Username:</strong> <code>$username</code></p>
        <p><strong>Password:</strong> <code>$new_password</code></p>
        <p><strong>Nama:</strong> $nama_lengkap</p>
    </div>";
    
    echo "<hr>";
    
    
    echo "<h3>🔍 Verifikasi Password Hash:</h3>";
    echo "<div class='info-box'>";
    echo "<p><strong>Hash:</strong><br><code style='word-break: break-all;'>$hashed_password</code></p>";
    
    if (password_verify($new_password, $hashed_password)) {
        echo "<div class='success'>
            <p><strong>✓ Password hash VALID!</strong></p>
            <p>Password dapat digunakan untuk login.</p>
        </div>";
    } else {
        echo "<div class='warning'>
            <p><strong>✗ Password hash TIDAK VALID!</strong></p>
            <p>Ada masalah dengan hashing. Coba refresh halaman ini.</p>
        </div>";
    }
    echo "</div>";
    
    echo "<hr>";
    
    echo "<div class='warning'>
        <p><strong>⚠️ PENTING - KEAMANAN:</strong></p>
        <p>1. Segera HAPUS file <code>reset_password.php</code> ini setelah selesai!</p>
        <p>2. File ini mengandung informasi sensitif.</p>
        <p>3. Jangan biarkan file ini accessible di production.</p>
    </div>";
    
    echo "<a href='admin/admin_login.php' class='btn'>🔐 Login Sekarang</a>";
    
} else {
    echo "<h2 class='error'> Gagal Reset Password!</h2>";
    echo "<div class='warning'>";
    echo "<p><strong>Error:</strong> " . $conn->error . "</p>";
    echo "<p>Pastikan database sudah diimport dengan benar.</p>";
    echo "</div>";
}

echo "    </div>
</body>
</html>";

$stmt->close();
$conn->close();
?>