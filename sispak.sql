CREATE DATABASE sistem_pakar_gigi;
USE sistem_pakar_gigi;

-- Tabel Pengguna
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Penyakit
CREATE TABLE penyakit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_penyakit VARCHAR(10) NOT NULL UNIQUE,
    nama_penyakit VARCHAR(100) NOT NULL,
    deskripsi TEXT NOT NULL,
    solusi TEXT NOT NULL
);

-- Tabel Gejala
CREATE TABLE gejala (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_gejala VARCHAR(10) NOT NULL UNIQUE,
    nama_gejala VARCHAR(100) NOT NULL
);

-- Tabel Aturan (Hubungan Gejala-Penyakit)
CREATE TABLE aturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penyakit_id INT NOT NULL,
    gejala_id INT NOT NULL,
    FOREIGN KEY (penyakit_id) REFERENCES penyakit(id),
    FOREIGN KEY (gejala_id) REFERENCES gejala(id)
);

-- Tabel Riwayat Diagnosa
CREATE TABLE riwayat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    penyakit_id INT NOT NULL,
    tanggal DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (penyakit_id) REFERENCES penyakit(id)
);

-- Data awal penyakit (sesuai dokumen)
INSERT INTO penyakit (kode_penyakit, nama_penyakit, deskripsi, solusi) VALUES
('P01', 'Karies gigi', 'Karies atau gigi berlubang adalah demineralisasi dari bagian anorganik gigi dengan pelarutan substansi organik yang dikarenakan oleh penyebab multifaktorial.', 'Perawatan gigi berlubang, penambalan gigi, atau perawatan saluran akar jika diperlukan.'),
('P02', 'Erosi Gigi', 'Erosi gigi adalah kehilangan jaringan gigi secara progresif dan irreversible yang disebabkan karena kimiawi dari asam secara intrinsik maupun ekstrinsik.', 'Mengurangi konsumsi makanan/minuman asam, menggunakan pasta gigi khusus, dan perawatan gigi sensitif.'),
('P03', 'Gingivitis', 'Gingivitis atau peradangan gusi adalah inflamasi pada mukosa skuamosa atau gingiva atau jaringan lunak sekitar gigi.', 'Pembersihan karang gigi, perbaikan kebersihan mulut, dan penggunaan obat kumur antiseptik.'),
('P04', 'Pulpitis', 'Pulpitis adalah peradangan pada pulpa gigi yang menimbulkan rasa nyeri. Pulpa adalah bagian gigi yang paling dalam, yang mengandung saraf dan pembuluh darah.', 'Perawatan saluran akar atau pencabutan gigi jika sudah parah.'),
('P05', 'Abses Gigi', 'Abses adalah rongga patologis yang berisi nanah yang disebabkan oleh infeksi bakteri. Ini merupakan infeksi akut purulen yang berkembang pada bagian apikal gigi.', 'Pemberian antibiotik, drainase abses, dan perawatan saluran akar atau pencabutan gigi.'),
('P06', 'Periodontitis', 'Periodontitis adalah suatu proses inflamasi yang mempengaruhi struktur penyangga gigi (ligament periodontal), tulang alveolar dan sementum.', 'Pembersihan karang gigi dalam, perawatan periodontal, dan dalam kasus parah mungkin perlu operasi.'),
('P07', 'Halitosis', 'Halitosis atau bau mulut adalah kondisi yang ditandai dengan aroma napas mulut yang tidak sedap.', 'Perbaikan kebersihan mulut, pembersihan lidah, perawatan gigi berlubang, dan penggunaan obat kumur.');

-- Data awal gejala (sesuai dokumen)
INSERT INTO gejala (kode_gejala, nama_gejala) VALUES
('G01', 'Gusi bengkak'),
('G02', 'Gigi ngilu'),
('G03', 'Bau mulut tak sedap'),
('G04', 'Gusi sakit saat disentuh'),
('G05', 'Gigi sakit saat makan/minum yang panas atau dingin'),
('G06', 'Gusi berdarah'),
('G07', 'Gusi nyeri'),
('G08', 'Gusi bernanah'),
('G09', 'Gigi goyang'),
('G10', 'Penumpukan plak/banyak karang gigi'),
('G11', 'Gigi berlubang'),
('G12', 'Gigi patah'),
('G13', 'Gigi sakit terus menerus'),
('G14', 'Terlihat noda hitam, coklat atau putih pada permukaan'),
('G15', 'Gigi tampak kuning'),
('G16', 'Radang gusi'),
('G17', 'Gusi mengkilap'),
('G18', 'Mulut tak sedap/pahit'),
('G19', 'Gigi renggang'),
('G20', 'Gusi memerah/keunguan'),
('G21', 'Gusi yang terdorong maju membuat gigi terlihat panjang/tonggos'),
('G22', 'Nyeri saat mengunyah'),
('G23', 'Mulut menjadi kering'),
('G24', 'Adanya lapisan pada lidah');

-- Data awal aturan (hubungan gejala-penyakit)
-- Contoh beberapa aturan (sesuai tabel keputusan di dokumen)
-- Karies Gigi (P01)
INSERT INTO aturan (penyakit_id, gejala_id) VALUES 
(1, 2), -- G02: Gigi ngilu
(1, 3), -- G03: Bau mulut tak sedap
(1, 11), -- G11: Gigi berlubang
(1, 12),
(1, 13), -- G13: Gigi sakit terus menerus
(1, 14); -- G14: Terlihat noda hitam, coklat atau putih pada permukaan

-- Erosi Gigi (P02)
INSERT INTO aturan (penyakit_id, gejala_id) VALUES 
(2, 2),
(2, 9),
(2, 12), -- G12: Gigi patah
(2, 15), -- G15: Gigi tampak kuning
(2, 19);

-- Gingivitis (P03)
INSERT INTO aturan (penyakit_id, gejala_id) VALUES 
(3, 1), -- G01: Gusi bengkak
(3, 3), -- G03: Bau mulut tak sedap
(3, 4), -- G04: Gusi sakit saat disentuh
(3, 5), -- G05: Gigi sakit saat makan/minum yang panas atau dingin
(3, 6), -- G06: Gusi berdarah
(3, 7), -- G07: Gusi nyeri
(3, 9),
(3, 10), -- G10: Penumpukan plak/banyak karang gigi
(3, 16), -- G16: Radang gusi
(3, 17), -- G17: Gusi mengkilap
(3, 18),
(3, 20), -- G20: Gusi memerah/keunguan
(3, 22);

-- Pulpitis (P04)
INSERT INTO aturan (penyakit_id, gejala_id) VALUES 
(4, 2), -- G02: Gigi ngilu
(4, 3), -- G03: Bau mulut tak sedap
(4, 4),
(4, 5), -- G05: Gigi sakit saat makan/minum yang panas atau dingin
(4, 6),
(4, 11),
(4, 13), -- G13: Gigi sakit terus menerus
(4, 22); -- G22: Nyeri saat mengunyah

-- Abses Gigi (P05)
INSERT INTO aturan (penyakit_id, gejala_id) VALUES 
(5, 1), -- G01: Gusi bengkak
(5, 3), -- G03: Bau mulut tak sedap
(5, 4),
(5, 7), -- G07: Gusi nyeri
(5, 8), -- G08: Gusi bernanah
(5, 9),
(5, 11),
(5, 13), -- G13: Gigi sakit terus menerus
(5, 16),
(5, 18),
(5, 20),
(5, 21),
(5, 22); -- G22: Nyeri saat mengunyah

-- Periodontitis (P06)
INSERT INTO aturan (penyakit_id, gejala_id) VALUES 
(6, 1), -- G01: Gusi bengkak
(6, 3), -- G03: Bau mulut tak sedap
(6, 4), -- G04: Gusi sakit saat disentuh
(6, 6), -- G06: Gusi berdarah
(6, 7),
(6, 8),
(6, 9), -- G09: Gigi goyang
(6, 10), -- G10: Penumpukan plak/banyak karang gigi
(6, 16), -- G16: Radang gusi
(6, 19), -- G18: Mulut tak sedap/pahit
(6, 20), -- G19: Gigi renggang
(6, 21), -- G21: Gusi yang terdorong maju membuat gigi terlihat panjang/tonggos
(6, 22), -- G22: Nyeri saat mengunyah
(6, 23);

-- Halitosis (P07)
INSERT INTO aturan (penyakit_id, gejala_id) VALUES 
(7, 3), -- G03: Bau mulut tak sedap
(7, 10), -- G10: Penumpukan plak/banyak karang gigi
(7, 18), -- G18: Mulut tak sedap/pahit
(7, 23), -- G23: Mulut menjadi kering
(7, 24); -- G24: Adanya lapisan pada lidah
-- Buat akun admin awal
INSERT INTO users (nama, email, password, is_admin) VALUES 
('Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
-- Password: password