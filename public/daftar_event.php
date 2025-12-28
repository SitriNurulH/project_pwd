<?php 
require_once '../config/db_connect.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event - Sistem Event Kampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>📅 Event Kampus</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="daftar_event.php" class="active">Daftar Event</a></li>
                <li><a href="cek_status.php">Cek Status</a></li>
                <li><a href="../admin/admin_login.php">Admin Login</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <section class="page-header">
            <h2>Semua Event Kampus</h2>
            <p>Temukan event yang sesuai dengan minatmu</p>
        </section>

        <div class="search-container">
            <input 
                type="text" 
                id="search-input" 
                class="search-input" 
                placeholder="🔍 Cari event berdasarkan nama atau lokasi..."
                autocomplete="off"
            >
        </div>

        <div id="event-list" class="event-grid-full">
            <p>Loading events...</p>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sistem Event Kampus. All rights reserved.</p>
        </div>
    </footer>

    <script>
        let searchTimeout;
        const searchInput = document.getElementById('search-input');
        const eventList = document.getElementById('event-list');

        
        document.addEventListener('DOMContentLoaded', function() {
            loadEvents();
        });

        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const keyword = this.value.trim();
                loadEvents(keyword);
            }, 300); 
        });

        function loadEvents(keyword = '') {
            const url = keyword 
                ? `../process/api_get_events.php?search=${encodeURIComponent(keyword)}`
                : '../process/api_get_events.php';

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.events.length > 0) {
                        displayEvents(data.events);
                    } else {
                        eventList.innerHTML = `
                            <div class="no-data">
                                <p>😔 Tidak ada event yang ditemukan.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    eventList.innerHTML = '<p>Gagal memuat data event.</p>';
                });
        }

        function displayEvents(events) {
            let html = '';
            events.forEach(event => {
                const sisa_kuota = event.kuota - event.jumlah_pendaftar;
                const statusQuota = sisa_kuota > 0 ? 'available' : 'full';
                
                html += `
                    <div class="event-card-full">
                        <div class="event-header">
                            <div class="event-date">
                                <span class="day">${formatDate(event.tanggal).day}</span>
                                <span class="month">${formatDate(event.tanggal).month}</span>
                                <span class="year">${formatDate(event.tanggal).year}</span>
                            </div>
                            <div class="event-title">
                                <h3>${event.nama_event}</h3>
                                <p class="narasumber">👤 ${event.narasumber || 'TBA'}</p>
                            </div>
                        </div>
                        <div class="event-body">
                            <p class="event-description">${event.deskripsi || 'Tidak ada deskripsi'}</p>
                            <div class="event-meta">
                                <span>📍 ${event.lokasi}</span>
                                <span>⏰ ${event.waktu}</span>
                                <span class="quota ${statusQuota}">
                                    👥 ${sisa_kuota}/${event.kuota} tersisa
                                </span>
                            </div>
                        </div>
                        <div class="event-footer">
                            ${sisa_kuota > 0 
                                ? `<a href="event_detail.php?id=${event.event_id}" class="btn btn-primary">Lihat Detail & Daftar</a>`
                                : `<button class="btn btn-disabled" disabled>Kuota Penuh</button>`
                            }
                        </div>
                    </div>
                `;
            });
            eventList.innerHTML = html;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return {
                day: date.getDate(),
                month: months[date.getMonth()],
                year: date.getFullYear()
            };
        }
    </script>
</body>
</html>