DROP DATABASE IF EXISTS db_seminar;
CREATE DATABASE db_seminar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_seminar;


CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB;


CREATE TABLE event (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_event VARCHAR(200) NOT NULL,
    tanggal DATE NOT NULL,
    waktu TIME NOT NULL,
    lokasi VARCHAR(200) NOT NULL,
    narasumber VARCHAR(200),
    deskripsi TEXT,
    kuota INT NOT NULL DEFAULT 0,
    banner VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_tanggal (tanggal),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB;


CREATE TABLE peserta (
    peserta_id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nim VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    jurusan VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_nim (nim),
    INDEX idx_email (email),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB;


CREATE TABLE pendaftaran (
    daftar_id INT AUTO_INCREMENT PRIMARY KEY,
    peserta_id INT NOT NULL,
    event_id INT NOT NULL,
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
    kode_unik VARCHAR(20) UNIQUE NOT NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (peserta_id) REFERENCES peserta(peserta_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_kode (kode_unik),
    INDEX idx_deleted (deleted_at),
    UNIQUE KEY unique_registration (peserta_id, event_id, deleted_at)
) ENGINE=InnoDB;



INSERT INTO admin (username, password, nama_lengkap) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');


INSERT INTO event (nama_event, tanggal, waktu, lokasi, narasumber, deskripsi, kuota) VALUES 
('Workshop Web Development', '2025-01-15', '09:00:00', 'Lab Komputer A', 'Dr. Budi Santoso', 'Workshop intensif tentang pengembangan web modern menggunakan teknologi terkini seperti HTML5, CSS3, JavaScript, PHP dan MySQL. Peserta akan belajar membuat website dari nol hingga deployment.', 50),
('Seminar Kecerdasan Buatan', '2025-01-20', '13:00:00', 'Auditorium Utama', 'Prof. Ani Wijaya, M.Kom', 'Seminar tentang perkembangan AI dan Machine Learning serta implementasinya dalam berbagai bidang seperti kesehatan, pendidikan, dan industri. Akan ada demo aplikasi AI yang menarik.', 100),
('Pelatihan UI/UX Design', '2025-01-25', '08:00:00', 'Ruang Multimedia', 'Candra Prakoso, S.Kom', 'Pelatihan desain antarmuka dan pengalaman pengguna untuk aplikasi mobile dan web. Peserta akan belajar Figma, Adobe XD, dan prinsip-prinsip design thinking.', 30),
('Bootcamp Mobile App Development', '2025-02-01', '08:00:00', 'Lab Komputer B', 'Dian Sari, M.T.', 'Bootcamp intensive selama 3 hari untuk belajar membuat aplikasi mobile menggunakan React Native. Cocok untuk pemula yang ingin terjun ke dunia mobile development.', 25),
('Seminar Cyber Security', '2025-02-10', '14:00:00', 'Ruang Seminar Lt.3', 'Ir. Ahmad Fauzi, M.Sc', 'Membahas tentang keamanan siber, ethical hacking, dan cara melindungi sistem dari serangan cyber. Akan ada demo penetration testing.', 80);


INSERT INTO peserta (nama, nim, email, no_hp, jurusan) VALUES 
('Ahmad Rizki Pratama', '2021001', 'ahmad.rizki@mail.com', '081234567890', 'Teknik Informatika'),
('Siti Nurhaliza', '2021002', 'siti.nur@mail.com', '081234567891', 'Sistem Informasi'),
('Budi Santoso', '2021003', 'budi.santoso@mail.com', '081234567892', 'Teknik Komputer'),
('Dewi Lestari', '2021004', 'dewi.lestari@mail.com', '081234567893', 'Teknik Informatika');


INSERT INTO pendaftaran (peserta_id, event_id, status, kode_unik) VALUES 
(1, 1, 'diterima', 'EVT-2025-A1B2C3'),
(2, 1, 'pending', 'EVT-2025-D4E5F6'),
(3, 2, 'diterima', 'EVT-2025-G7H8I9'),
(4, 2, 'pending', 'EVT-2025-J1K2L3');