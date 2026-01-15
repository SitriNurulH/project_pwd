<?php
$password = "admin123"; 

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p>Password: <strong>$password</strong></p>";
echo "<p>Hash: <strong>$hash</strong></p>";
echo "<hr>";
echo "<p>Copy hash di atas dan gunakan untuk update password di database.</p>";


if (password_verify($password, $hash)) {
    echo "<p style='color: green;'>✓ Verifikasi berhasil! Hash ini valid untuk password tersebut.</p>";
} else {
    echo "<p style='color: red;'>✗ Verifikasi gagal!</p>";
}
?>