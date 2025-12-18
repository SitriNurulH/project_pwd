-- Database: db_seminar

CREATE DATABASE IF NOT EXISTS db_seminar;
USE db_seminar;

-- Tabel Admin
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

-- Tabel Event
CREATE TABLE event (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_event VARCHAR(200) NOT NULL,
    tanggal DATE NOT NULL,
    waktu TIME NOT NULL,
    lokasi VARCHAR(200) NOT NULL,
    narasumber VARCHAR(200),
    deskripsi TEXT,
    kuota INT NOT NULL,
    banner VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

-- Tabel Peserta
CREATE TABLE peserta (
    peserta_id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nim VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    jurusan VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

-- Tabel Pendaftaran
CREATE TABLE pendaftaran (
    daftar_id INT AUTO_INCREMENT PRIMARY KEY,
    peserta_id INT NOT NULL,
    event_id INT NOT NULL,
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
    kode_unik VARCHAR(20) UNIQUE,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (peserta_id) REFERENCES peserta(peserta_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (peserta_id, event_id, deleted_at)
);

-- Insert data admin default (password: admin123)
INSERT INTO admin (username, password, nama_lengkap) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Insert data event contoh
INSERT INTO event (nama_event, tanggal, waktu, lokasi, narasumber, deskripsi, kuota) VALUES 
('Workshop Web Development', '2025-01-15', '09:00:00', 'Lab Komputer A', 'Dr. Budi Santoso', 'Workshop intensif tentang pengembangan web modern menggunakan teknologi terkini.', 50),
('Seminar Kecerdasan Buatan', '2025-01-20', '13:00:00', 'Auditorium Utama', 'Prof. Ani Wijaya', 'Seminar tentang perkembangan AI dan implementasinya dalam berbagai bidang.', 100),
('Pelatihan UI/UX Design', '2025-01-25', '08:00:00', 'Ruang Multimedia', 'Candra Prakoso, S.Kom', 'Pelatihan desain antarmuka dan pengalaman pengguna untuk aplikasi mobile dan web.', 30);

-- Insert data peserta contoh
INSERT INTO peserta (nama, nim, email, no_hp, jurusan) VALUES 
('Ahmad Rizki', '2021001', 'ahmad.rizki@mail.com', '081234567890', 'Teknik Informatika'),
('Siti Nurhaliza', '2021002', 'siti.nur@mail.com', '081234567891', 'Sistem Informasi');

-- Insert data pendaftaran contoh
INSERT INTO pendaftaran (peserta_id, event_id, status, kode_unik) VALUES 
(1, 1, 'diterima', 'EVT-2025-001'),
(2, 1, 'pending', 'EVT-2025-002');