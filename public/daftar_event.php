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
    <style>
    :root {
        --primary: #667eea;
        --secondary: #764ba2;
        --bg: #f6f7fb;
        --text: #111827;
        --muted: #6b7280;
        --border: #e5e7eb;
        --card: #ffffff;
        --shadow: 0 10px 25px rgba(17,24,39,0.08);
        --shadow-hover: 0 18px 45px rgba(17,24,39,0.12);
        --radius: 16px;
    }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }

    /* NAVBAR */
    .navbar {
        position: sticky;
        top: 0;
        z-index: 50;
        backdrop-filter: blur(10px);
        background: rgba(255,255,255,0.8);
        border-bottom: 1px solid rgba(229,231,235,0.7);
    }

    .navbar .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.9rem 1rem;
        gap: 1rem;
    }

    .nav-brand h1 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-menu {
        list-style: none;
        display: flex;
        gap: 0.6rem;
        padding: 0;
        margin: 0;
        align-items: center;
    }

    .nav-menu a {
        text-decoration: none;
        padding: 0.55rem 0.95rem;
        border-radius: 999px;
        color: #374151;
        font-weight: 600;
        transition: 0.25s;
        border: 1px solid transparent;
    }

    .nav-menu a:hover {
        background: rgba(102,126,234,0.12);
        border-color: rgba(102,126,234,0.25);
        transform: translateY(-1px);
    }

    .nav-menu a.active {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 10px 20px rgba(102,126,234,0.25);
    }

    /* HEADER */
    .page-header {
        margin-top: 1.5rem;
        background: linear-gradient(135deg, rgba(102,126,234,0.12), rgba(118,75,162,0.12));
        border: 1px solid rgba(102,126,234,0.18);
        padding: 1.5rem 1.5rem;
        border-radius: var(--radius);
        box-shadow: 0 12px 30px rgba(17,24,39,0.05);
    }

    .page-header h2 {
        margin: 0;
        font-size: 1.6rem;
        letter-spacing: 0.2px;
    }

    .page-header p {
        margin-top: 0.5rem;
        margin-bottom: 0;
        color: var(--muted);
        font-size: 1rem;
    }

    /* SEARCH */
    .search-container {
        max-width: 720px;
        margin: 1.5rem auto 0;
        position: relative;
    }

    .search-container::before {
        content: "🔍";
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
        font-size: 1rem;
    }

    .search-input {
        width: 100%;
        padding: 1rem 1.2rem 1rem 2.8rem;
        font-size: 1rem;
        border: 1px solid var(--border);
        border-radius: 999px;
        transition: all 0.25s ease;
        background: rgba(255,255,255,0.9);
        box-shadow: 0 10px 20px rgba(17,24,39,0.05);
    }

    .search-input:focus {
        outline: none;
        border-color: rgba(102,126,234,0.7);
        box-shadow: 0 0 0 4px rgba(102,126,234,0.15);
        background: #fff;
    }

    /* GRID */
    .event-grid-full {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
        padding-bottom: 2rem;
    }

    /* CARD */
    .event-card-full {
        background: var(--card);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        border: 1px solid rgba(229,231,235,0.8);
        transition: all 0.25s ease;
        transform: translateY(0);
    }

    .event-card-full:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(102,126,234,0.25);
    }

    .event-header {
        display: flex;
        gap: 1rem;
        padding: 1.3rem 1.3rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        align-items: center;
    }

    .event-date {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.18);
        padding: 0.75rem 0.9rem;
        border-radius: 14px;
        min-width: 78px;
        border: 1px solid rgba(255,255,255,0.25);
    }

    .event-date .day {
        font-size: 1.9rem;
        font-weight: 900;
        line-height: 1;
    }

    .event-date .month {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-top: 2px;
    }

    .event-date .year {
        font-size: 0.8rem;
        opacity: 0.85;
    }

    .event-title h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .narasumber {
        margin: 0.35rem 0 0;
        opacity: 0.95;
        font-size: 0.92rem;
        line-height: 1.2;
    }

    .event-body {
        padding: 1.2rem 1.3rem 1.1rem;
    }

    .event-description {
        color: var(--muted);
        line-height: 1.6;
        margin: 0 0 0.9rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 74px;
    }

    /* Meta chips */
    .event-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        font-size: 0.9rem;
    }

    .event-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.65rem;
        border-radius: 999px;
        background: rgba(17,24,39,0.04);
        color: #374151;
        border: 1px solid rgba(229,231,235,0.75);
    }

    .quota {
        font-weight: 800;
    }

    .quota.available {
        background: rgba(16,185,129,0.12) !important;
        border-color: rgba(16,185,129,0.25) !important;
        color: #047857 !important;
    }

    .quota.full {
        background: rgba(239,68,68,0.12) !important;
        border-color: rgba(239,68,68,0.25) !important;
        color: #b91c1c !important;
    }

    .event-footer {
        padding: 1.1rem 1.3rem 1.3rem;
        background: linear-gradient(180deg, rgba(249,250,251,1), rgba(255,255,255,1));
        border-top: 1px solid rgba(229,231,235,0.8);
    }

    /* Button */
    .btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        padding: 0.9rem 1.2rem;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 800;
        text-align: center;
        transition: all 0.25s ease;
        width: 100%;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 14px 25px rgba(102,126,234,0.25);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 35px rgba(102,126,234,0.35);
    }

    .btn-disabled {
        background: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* NO DATA + LOADING */
    .no-data, .loading {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3.5rem 1rem;
        color: var(--muted);
        background: rgba(255,255,255,0.7);
        border: 1px dashed rgba(229,231,235,1);
        border-radius: var(--radius);
    }

    .no-data p, .loading p {
        font-size: 1.05rem;
        margin: 0.5rem 0;
    }

    footer {
        margin-top: 2rem;
        padding: 1.2rem 0;
        color: var(--muted);
        border-top: 1px solid rgba(229,231,235,0.8);
        background: rgba(255,255,255,0.6);
        backdrop-filter: blur(10px);
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .navbar .container {
            flex-direction: column;
            align-items: flex-start;
        }

        .nav-menu {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    }

    @media (max-width: 768px) {
        .event-grid-full {
            grid-template-columns: 1fr;
        }

        .page-header {
            padding: 1.2rem;
        }
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
                placeholder="🔍 Cari event berdasarkan nama, lokasi, atau narasumber..."
                autocomplete="off"
            >
        </div>

        <div id="event-list" class="event-grid-full">
            <div class="loading">
                <p>⏳ Memuat data event...</p>
            </div>
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

        // Load events saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Loading initial events...');
            loadEvents();
        });

        // Live search dengan debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const keyword = this.value.trim();
                console.log('Searching for:', keyword);
                loadEvents(keyword);
            }, 300); // Delay 300ms setelah user berhenti mengetik
        });

        function loadEvents(keyword = '') {
            // Show loading
            eventList.innerHTML = '<div class="loading"><p>⏳ Mencari event...</p></div>';
            
            const url = keyword 
                ? `../process/api_get_events.php?search=${encodeURIComponent(keyword)}`
                : '../process/api_get_events.php';

            console.log('Fetching from:', url);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    
                    if (data.success && data.events && data.events.length > 0) {
                        displayEvents(data.events);
                    } else {
                        eventList.innerHTML = `
                            <div class="no-data">
                                <p>😔 Tidak ada event yang ditemukan.</p>
                                ${keyword ? `<p>Coba kata kunci lain atau <a href="daftar_event.php">lihat semua event</a></p>` : ''}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    eventList.innerHTML = `
                        <div class="no-data">
                            <p>❌ Gagal memuat data event.</p>
                            <p>Error: ${error.message}</p>
                            <button class="btn btn-primary" onclick="loadEvents()" style="max-width: 200px; margin: 1rem auto;">Coba Lagi</button>
                        </div>
                    `;
                });
        }

        function displayEvents(events) {
            let html = '';
            
            events.forEach(event => {
                const sisa_kuota = event.kuota - event.jumlah_pendaftar;
                const statusQuota = sisa_kuota > 0 ? 'available' : 'full';
                const dateFormatted = formatDate(event.tanggal);
                
                // Truncate description jika terlalu panjang
                let description = event.deskripsi || 'Tidak ada deskripsi';
                if (description.length > 150) {
                    description = description.substring(0, 150) + '...';
                }
                
                html += `
                    <div class="event-card-full">
                        <div class="event-header">
                            <div class="event-date">
                                <span class="day">${dateFormatted.day}</span>
                                <span class="month">${dateFormatted.month}</span>
                                <span class="year">${dateFormatted.year}</span>
                            </div>
                            <div class="event-title">
                                <h3>${escapeHtml(event.nama_event)}</h3>
                                <p class="narasumber">👤 ${escapeHtml(event.narasumber || 'Narasumber akan diumumkan')}</p>
                            </div>
                        </div>
                        <div class="event-body">
                            <p class="event-description">${escapeHtml(description)}</p>
                            <div class="event-meta">
                                <span>📍 ${escapeHtml(event.lokasi)}</span>
                                <span>⏰ ${event.waktu} WIB</span>
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
            
            // Smooth scroll ke hasil jika melakukan search
            if (searchInput.value.trim() !== '') {
                eventList.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
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

        // Helper function untuk escape HTML (prevent XSS)
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Auto-focus pada search input
        searchInput.focus();
    </script>
</body>
</html>