<?php 
require_once '../config/db_connect.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎉 Sistem Event Kampus - Beranda</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>📅 Event Kampus</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">🏠 Home</a></li>
                <li><a href="daftar_event.php">📋 Daftar Event</a></li>
                <li><a href="cek_status.php">🔍 Cek Status</a></li>
                <li><a href="../admin/admin_login.php">🔐 Admin</a></li>
            </ul>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h2>✨ Selamat Datang di Sistem Event Kampus ✨</h2>
            <p>Temukan dan daftar event menarik yang diadakan di kampus dengan mudah!</p>
            <a href="daftar_event.php" class="btn btn-primary">
                📋 Lihat Semua Event
            </a>
        </div>
    </header>

    <main class="container">
        <section class="events-section">
            <h3>🎯 Event Terbaru</h3>
            <div id="event-list" class="event-grid">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Memuat data event...</p>
                </div>
            </div>
        </section>

        <section class="features">
            <h3>💡 Mengapa Menggunakan Sistem Ini?</h3>
            <div class="feature-grid">
                <div class="feature-card">
                    <span class="icon">🎯</span>
                    <h4>Mudah & Cepat</h4>
                    <p>Pendaftaran event hanya butuh beberapa klik. Tidak perlu repot mengisi form manual!</p>
                </div>
                <div class="feature-card">
                    <span class="icon">📱</span>
                    <h4>Akses Kapan Saja</h4>
                    <p>Cek status pendaftaran dari mana saja dan kapan saja. 24/7 tersedia untuk Anda!</p>
                </div>
                <div class="feature-card">
                    <span class="icon">🔒</span>
                    <h4>Aman & Terpercaya</h4>
                    <p>Data Anda dijamin keamanannya dengan enkripsi dan validasi berlapis.</p>
                </div>
            </div>
        </section>

        <section style="background: white; padding: 2.5rem; border-radius: 20px; box-shadow: var(--shadow-md); margin-top: 3rem; text-align: center;">
            <h3 style="color: var(--primary-pink); margin-bottom: 1rem; font-size: 1.8rem;">📢 Cara Menggunakan Sistem</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div>
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">1️⃣</div>
                    <h4 style="color: var(--primary-pink); margin-bottom: 0.5rem;">Pilih Event</h4>
                    <p style="color: var(--text-light);">Browse dan pilih event yang kamu minati</p>
                </div>
                <div>
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">2️⃣</div>
                    <h4 style="color: var(--primary-pink); margin-bottom: 0.5rem;">Daftar</h4>
                    <p style="color: var(--text-light);">Isi form pendaftaran dengan data diri</p>
                </div>
                <div>
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">3️⃣</div>
                    <h4 style="color: var(--primary-pink); margin-bottom: 0.5rem;">Simpan Kode</h4>
                    <p style="color: var(--text-light);">Simpan kode pendaftaran yang diberikan</p>
                </div>
                <div>
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">4️⃣</div>
                    <h4 style="color: var(--primary-pink); margin-bottom: 0.5rem;">Cek Status</h4>
                    <p style="color: var(--text-light);">Pantau status verifikasi pendaftaran</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>💖 © 2025 Sistem Event Kampus - Dibuat dengan ❤️ untuk memudahkan pendaftaran event</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentEvents();
        });

        function loadRecentEvents() {
            fetch('../process/api_get_events.php?limit=3')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const eventList = document.getElementById('event-list');
                    
                    if (data.success && data.events && data.events.length > 0) {
                        let html = '';
                        data.events.forEach(event => {
                            const sisa_kuota = event.kuota - event.jumlah_pendaftar;
                            const statusClass = sisa_kuota > 0 ? 'available' : 'full';
                            
                            html += `
                                <div class="event-card">
                                    <div class="event-date">
                                        <span class="day">${formatDate(event.tanggal).day}</span>
                                        <span class="month">${formatDate(event.tanggal).month}</span>
                                    </div>
                                    <div class="event-content">
                                        <h4>${escapeHtml(event.nama_event)}</h4>
                                        <p class="event-info">
                                            <span>📍 ${escapeHtml(event.lokasi)}</span>
                                            <span>⏰ ${event.waktu} WIB</span>
                                        </p>
                                        <p class="event-quota ${statusClass}">
                                            👥 Kuota: <strong>${sisa_kuota}</strong> dari ${event.kuota}
                                        </p>
                                        <a href="event_detail.php?id=${event.event_id}" class="btn btn-secondary">
                                            📝 Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            `;
                        });
                        eventList.innerHTML = html;
                    } else {
                        eventList.innerHTML = `
                            <div class="no-data">
                                <p style="font-size: 3rem; margin-bottom: 1rem;">😔</p>
                                <p>Belum ada event tersedia saat ini.</p>
                                <p style="font-size: 0.9rem; margin-top: 0.5rem; opacity: 0.7;">Silakan cek kembali nanti!</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    document.getElementById('event-list').innerHTML = `
                        <div class="no-data">
                            <p style="font-size: 3rem; margin-bottom: 1rem;">❌</p>
                            <p>Gagal memuat data event.</p>
                            <p style="font-size: 0.9rem; margin-top: 0.5rem;">
                                <a href="javascript:location.reload()" style="color: var(--primary-pink); text-decoration: underline;">
                                    Klik di sini untuk refresh
                                </a>
                            </p>
                        </div>
                    `;
                });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return {
                day: date.getDate(),
                month: months[date.getMonth()]
            };
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>