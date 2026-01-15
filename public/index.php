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
        }

        body{
            background: var(--bg);
            color: var(--text);
            font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
        }

        /* NAVBAR */
        .navbar{
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.85);
            border-bottom: 1px solid rgba(229,231,235,0.7);
        }

        .navbar .container{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:1rem;
            padding: 0.9rem 1rem;
        }

        .nav-brand h1{
            margin:0;
            font-size:1.15rem;
            font-weight:900;
            display:flex;
            align-items:center;
            gap:0.55rem;
        }

        .nav-menu{
            list-style:none;
            display:flex;
            align-items:center;
            gap:0.6rem;
            padding:0;
            margin:0;
            flex-wrap: wrap;
        }

        .nav-menu a{
            text-decoration:none;
            padding:0.55rem 0.95rem;
            border-radius:999px;
            color:#374151;
            font-weight:700;
            transition:0.25s;
            border:1px solid transparent;
        }

        .nav-menu a:hover{
            background: rgba(102,126,234,0.12);
            border-color: rgba(102,126,234,0.25);
            transform: translateY(-1px);
        }

        .nav-menu a.active{
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color:white;
            box-shadow: 0 10px 20px rgba(102,126,234,0.25);
        }

        /* HERO */
        .hero{
            margin-top:1.2rem;
            border-radius: var(--radius);
            overflow:hidden;
            box-shadow: var(--shadow-hover);
            border:1px solid rgba(229,231,235,0.9);
            background: #fff;
        }

        .hero-inner{
            padding: 2.2rem 1.8rem;
            background: linear-gradient(135deg, rgba(102,126,234,0.16), rgba(118,75,162,0.16));
            position: relative;
        }

        .hero-inner::after{
            content:"";
            position:absolute;
            inset:0;
            background:
                radial-gradient(circle at top left, rgba(102,126,234,0.18), transparent 55%),
                radial-gradient(circle at bottom right, rgba(118,75,162,0.18), transparent 55%);
            pointer-events:none;
        }

        .hero-content{
            position:relative;
            z-index:2;
            max-width: 850px;
        }

        .hero h2{
            margin:0;
            font-size:2rem;
            font-weight:900;
            line-height:1.2;
            letter-spacing:0.2px;
        }

        .hero p{
            margin:0.8rem 0 1.2rem;
            color: var(--muted);
            font-size: 1.05rem;
            font-weight:600;
            line-height:1.6;
        }

        .hero-actions{
            display:flex;
            gap:0.8rem;
            flex-wrap:wrap;
            margin-top: 1.1rem;
        }

        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:0.55rem;
            padding:0.95rem 1.2rem;
            border-radius:16px;
            text-decoration:none;
            font-weight:900;
            border:none;
            cursor:pointer;
            transition:0.25s;
        }

        .btn-primary{
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color:white;
            box-shadow: 0 16px 28px rgba(102,126,234,0.28);
        }

        .btn-primary:hover{
            transform: translateY(-2px);
            box-shadow: 0 20px 38px rgba(102,126,234,0.35);
        }

        .btn-outline{
            background: rgba(255,255,255,0.85);
            color:#1f3b8a;
            border:1px solid rgba(102,126,234,0.25);
            box-shadow: 0 12px 24px rgba(17,24,39,0.06);
        }

        .btn-outline:hover{
            transform: translateY(-2px);
            background:#fff;
        }

        /* SECTIONS */
        .section-card{
            background:#fff;
            border:1px solid rgba(229,231,235,0.9);
            box-shadow: var(--shadow);
            border-radius: var(--radius);
            padding: 1.6rem;
            margin-top: 1.5rem;
        }

        .section-title{
            margin:0 0 1rem;
            font-size:1.25rem;
            font-weight:900;
        }

        /* EVENT GRID */
        .event-grid{
            display:grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.2rem;
            margin-top: 1rem;
        }

        .event-card{
            border-radius: var(--radius);
            border:1px solid rgba(229,231,235,0.85);
            overflow:hidden;
            background:#fff;
            box-shadow: 0 12px 22px rgba(17,24,39,0.05);
            transition: 0.25s ease;
        }

        .event-card:hover{
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(102,126,234,0.25);
        }

        .event-top{
            padding: 1.1rem 1.2rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            display:flex;
            align-items:center;
            gap: 1rem;
        }

        .event-date{
            min-width: 72px;
            padding: 0.7rem 0.8rem;
            border-radius: 14px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.25);
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
        }

        .event-date .day{
            font-size: 1.6rem;
            font-weight: 900;
            line-height:1;
        }

        .event-date .month{
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing:0.6px;
            margin-top:2px;
        }

        .event-title{
            margin:0;
            font-weight: 900;
            font-size: 1.1rem;
            line-height:1.25;
        }

        .event-speaker{
            margin:0.35rem 0 0;
            opacity:0.95;
            font-weight: 600;
            font-size:0.92rem;
        }

        .event-body{
            padding: 1.1rem 1.2rem 1.2rem;
        }

        .event-info{
            display:flex;
            flex-wrap:wrap;
            gap:0.55rem;
            margin: 0.7rem 0 0.6rem;
        }

        .chip{
            display:inline-flex;
            align-items:center;
            gap:0.35rem;
            padding:0.4rem 0.65rem;
            border-radius:999px;
            border:1px solid rgba(229,231,235,0.85);
            background: rgba(17,24,39,0.04);
            color:#374151;
            font-weight:700;
            font-size:0.9rem;
        }

        .quota{
            font-weight: 900;
        }

        .available{
            background: rgba(16,185,129,0.12) !important;
            border-color: rgba(16,185,129,0.25) !important;
            color:#047857 !important;
        }

        .full{
            background: rgba(239,68,68,0.12) !important;
            border-color: rgba(239,68,68,0.25) !important;
            color:#b91c1c !important;
        }

        .btn-secondary{
            width:100%;
            margin-top:0.7rem;
            background: rgba(102,126,234,0.12);
            border:1px solid rgba(102,126,234,0.22);
            color:#1f3b8a;
            border-radius:16px;
            padding:0.85rem 1rem;
            font-weight:900;
            display:inline-flex;
            justify-content:center;
            text-decoration:none;
            transition:0.25s;
        }

        .btn-secondary:hover{
            background: rgba(102,126,234,0.18);
            transform: translateY(-2px);
        }

        /* LOADING + NO DATA */
        .loading, .no-data{
            grid-column: 1 / -1;
            text-align:center;
            padding: 2.4rem 1rem;
            border: 1px dashed rgba(229,231,235,1);
            border-radius: var(--radius);
            background: rgba(255,255,255,0.7);
            color: var(--muted);
        }

        .spinner{
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 4px solid rgba(102,126,234,0.25);
            border-top-color: rgba(102,126,234,1);
            margin: 0 auto 0.9rem;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin{
            to { transform: rotate(360deg); }
        }

        /* FEATURES */
        .feature-grid{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .feature-card{
            background: rgba(255,255,255,0.85);
            border:1px solid rgba(229,231,235,0.9);
            border-radius: var(--radius);
            padding: 1.2rem;
            box-shadow: 0 12px 22px rgba(17,24,39,0.04);
            transition:0.25s;
        }

        .feature-card:hover{
            transform: translateY(-4px);
            box-shadow: var(--shadow);
            border-color: rgba(102,126,234,0.25);
        }

        .feature-card .icon{
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display:flex;
            align-items:center;
            justify-content:center;
            background: rgba(102,126,234,0.12);
            border: 1px solid rgba(102,126,234,0.22);
            font-size: 1.4rem;
            margin-bottom: 0.7rem;
        }

        .feature-card h4{
            margin:0 0 0.35rem;
            font-size: 1.05rem;
            font-weight: 900;
        }

        .feature-card p{
            margin:0;
            color: var(--muted);
            line-height:1.6;
            font-weight: 600;
        }

        /* STEPS */
        .steps-grid{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .step-card{
            background: rgba(255,255,255,0.9);
            border:1px solid rgba(229,231,235,0.9);
            border-radius: var(--radius);
            padding: 1.2rem;
            text-align:center;
            box-shadow: 0 12px 22px rgba(17,24,39,0.04);
        }

        .step-number{
            font-size: 2.2rem;
            margin-bottom: 0.3rem;
        }

        .step-card h4{
            margin:0.35rem 0 0.35rem;
            font-weight: 900;
        }

        .step-card p{
            margin:0;
            color: var(--muted);
            font-weight: 600;
        }

        footer{
            margin-top: 2rem;
            padding: 1.2rem 0;
            color: var(--muted);
            border-top: 1px solid rgba(229,231,235,0.8);
            background: rgba(255,255,255,0.65);
            backdrop-filter: blur(10px);
        }

        @media (max-width: 900px){
            .navbar .container{
                flex-direction:column;
                align-items:flex-start;
            }
        }

        @media (max-width: 768px){
            .hero-inner{
                padding: 1.6rem 1.2rem;
            }
            .hero h2{
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>Event Kampus</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active"> Home</a></li>
                <li><a href="daftar_event.php">Daftar Event</a></li>
                <li><a href="cek_status.php"> Cek Status</a></li>
                <li><a href="../admin/admin_login.php"> Admin Login</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <!-- HERO -->
        <header class="hero">
            <div class="hero-inner">
                <div class="hero-content">
                    <h2>Selamat Datang di Sistem Event Kampus</h2>
                    <p>
                        Temukan dan daftar event menarik yang diadakan di kampus dengan mudah.
                        Cek status pendaftaranmu kapan saja!
                    </p>

                    <div class="hero-actions">
                        <a href="daftar_event.php" class="btn btn-primary">📋 Lihat Semua Event</a>
                        <a href="cek_status.php" class="btn btn-outline">🔍 Cek Status</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- EVENT TERBARU -->
        <section class="section-card">
            <h3 class="section-title">Event Terbaru</h3>
            <div id="event-list" class="event-grid">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Memuat data event...</p>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section class="section-card">
            <h3 class="section-title">Mengapa Menggunakan Sistem Ini?</h3>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="icon">⚡</div>
                    <h4>Mudah & Cepat</h4>
                    <p>Pendaftaran event cukup beberapa klik, lebih praktis dan hemat waktu.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📱</div>
                    <h4>Akses Kapan Saja</h4>
                    <p>Cek status pendaftaran 24/7, dari mana saja tanpa ribet.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">🔒</div>
                    <h4>Aman & Terpercaya</h4>
                    <p>Data tersimpan aman, didukung validasi dan sistem yang rapi.</p>
                </div>
            </div>
        </section>

        <!-- STEPS -->
        <section class="section-card">
            <h3 class="section-title">Cara Menggunakan Sistem</h3>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1️⃣</div>
                    <h4>Pilih Event</h4>
                    <p>Temukan event yang kamu minati.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2️⃣</div>
                    <h4>Daftar</h4>
                    <p>Isi form pendaftaran dengan lengkap.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3️⃣</div>
                    <h4>Simpan Kode</h4>
                    <p>Simpan kode pendaftaran sebagai bukti.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4️⃣</div>
                    <h4>Cek Status</h4>
                    <p>Pantau status pendaftaranmu kapan saja.</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>💜 © 2025 Sistem Event Kampus — dibuat untuk memudahkan pendaftaran event kampus.</p>
        </div>
    </footer>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    loadRecentEvents();
});

function loadRecentEvents() {
    const apiUrl = '../process/api_get_events.php?limit=3';

    fetch(apiUrl)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const eventList = document.getElementById('event-list');

            console.log("API DATA:", data); // ✅ buat debug

            if (data.success && data.events && data.events.length > 0) {
                let html = '';

                data.events.forEach(event => {
                    const kuota = parseInt(event.kuota) || 0;
                    const daftar = parseInt(event.jumlah_pendaftar) || 0;
                    const sisa_kuota = kuota - daftar;

                    const statusClass = sisa_kuota > 0 ? 'available' : 'full';
                    const dateFmt = formatDate(event.tanggal);

                    html += `
                        <div class="event-card">
                            <div class="event-top">
                                <div class="event-date">
                                    <span class="day">${dateFmt.day}</span>
                                    <span class="month">${dateFmt.month}</span>
                                </div>
                                <div>
                                    <h4 class="event-title">${escapeHtml(event.nama_event)}</h4>
                                    <p class="event-speaker">👤 ${escapeHtml(event.narasumber || 'Narasumber akan diumumkan')}</p>
                                </div>
                            </div>

                            <div class="event-body">
                                <div class="event-info">
                                    <span class="chip">📍 ${escapeHtml(event.lokasi)}</span>
                                    <span class="chip">⏰ ${event.waktu} WIB</span>
                                    <span class="chip quota ${statusClass}">
                                        👥 ${sisa_kuota}/${kuota} tersisa
                                    </span>
                                </div>

                                <a href="event_detail.php?id=${event.event_id}" class="btn-secondary">
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
                        <p style="font-size: 2.5rem; margin:0 0 0.6rem;">😔</p>
                        <p>Belum ada event tersedia saat ini.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading events:', error);

            document.getElementById('event-list').innerHTML = `
                <div class="no-data">
                    <p style="font-size: 2.5rem; margin:0 0 0.6rem;">❌</p>
                    <p>Gagal memuat data event.</p>
                    <p style="font-size:0.9rem; opacity:0.75;">
                        <a href="javascript:location.reload()" style="text-decoration:underline; font-weight:800; color:#1f3b8a;">
                            Klik untuk refresh
                        </a>
                    </p>
                </div>
            `;
        });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
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
