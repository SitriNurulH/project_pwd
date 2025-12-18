<?php 
require_once '../config/db_connect.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Event Kampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>📅 Event Kampus</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="daftar_event.php">Daftar Event</a></li>
                <li><a href="cek_status.php">Cek Status</a></li>
                <li><a href="../admin/admin_login.php">Admin Login</a></li>
            </ul>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h2>Selamat Datang di Sistem Event Kampus</h2>
            <p>Temukan dan daftar event menarik yang diadakan di kampus</p>
            <a href="daftar_event.php" class="btn btn-primary">Lihat Semua Event</a>
        </div>
    </header>

    <main class="container">
        <section class="events-section">
            <h3>Event Terbaru</h3>
            <div id="event-list" class="event-grid">
                <p>Loading events...</p>
            </div>
        </section>

        <section class="features">
            <h3>Mengapa Menggunakan Sistem Ini?</h3>
            <div class="feature-grid">
                <div class="feature-card">
                    <span class="icon">🎯</span>
                    <h4>Mudah & Cepat</h4>
                    <p>Pendaftaran event hanya butuh beberapa klik</p>
                </div>
                <div class="feature-card">
                    <span class="icon">📱</span>
                    <h4>Akses Kapan Saja</h4>
                    <p>Cek status pendaftaran dari mana saja</p>
                </div>
                <div class="feature-card">
                    <span class="icon">🔒</span>
                    <h4>Aman & Terpercaya</h4>
                    <p>Data Anda dijamin keamanannya</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sistem Event Kampus. All rights reserved.</p>
        </div>
    </footer>

    <script src="../assets/js/script.js"></script>
    <script>
        // Load events menggunakan AJAX (Fetch API)
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentEvents();
        });

        function loadRecentEvents() {
            fetch('../process/api_get_events.php?limit=3')
                .then(response => response.json())
                .then(data => {
                    const eventList = document.getElementById('event-list');
                    
                    if (data.success && data.events.length > 0) {
                        let html = '';
                        data.events.forEach(event => {
                            const sisa_kuota = event.kuota - event.jumlah_pendaftar;
                            html += `
                                <div class="event-card">
                                    <div class="event-date">
                                        <span class="day">${formatDate(event.tanggal).day}</span>
                                        <span class="month">${formatDate(event.tanggal).month}</span>
                                    </div>
                                    <div class="event-content">
                                        <h4>${event.nama_event}</h4>
                                        <p class="event-info">
                                            <span>📍 ${event.lokasi}</span>
                                            <span>⏰ ${event.waktu}</span>
                                        </p>
                                        <p class="event-quota">
                                            Kuota: <strong>${sisa_kuota}</strong> dari ${event.kuota}
                                        </p>
                                        <a href="event_detail.php?id=${event.event_id}" class="btn btn-secondary">Lihat Detail</a>
                                    </div>
                                </div>
                            `;
                        });
                        eventList.innerHTML = html;
                    } else {
                        eventList.innerHTML = '<p>Belum ada event tersedia.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    document.getElementById('event-list').innerHTML = '<p>Gagal memuat data event.</p>';
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
    </script>
</body>
</html>