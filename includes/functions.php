<?php
require_once(__DIR__ . '/../config/database.php');

// Fungsi untuk menghitung probabilitas penyakit menggunakan Naïve Bayes
function hitungNaiveBayes($gejala_terpilih) {
    global $conn;
    
    if (empty($gejala_terpilih)) {
        return [
            'error' => true,
            'message' => 'Tidak ada gejala yang dipilih. Silakan pilih minimal satu gejala.'
        ];
    }
    
    // Ambil semua penyakit
    $penyakit_query = "SELECT * FROM penyakit";
    $penyakit_result = mysqli_query($conn, $penyakit_query);
    $penyakit_list = [];
    
    while ($row = mysqli_fetch_assoc($penyakit_result)) {
        $penyakit_list[$row['id']] = $row;
    }
    
    // Ambil semua aturan (hubungan gejala-penyakit)
    $aturan_query = "SELECT * FROM aturan";
    $aturan_result = mysqli_query($conn, $aturan_query);
    $aturan_list = [];
    
    while ($row = mysqli_fetch_assoc($aturan_result)) {
        $aturan_list[$row['penyakit_id']][$row['gejala_id']] = 1;
    }
    
    // Hitung total penyakit
    $total_penyakit = count($penyakit_list);
    $p = 1 / $total_penyakit; 

    // Hitung total gejala
    $total_gejala = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as total FROM gejala"))['total'] ?? 1;

    
    // Hitung probabilitas untuk setiap penyakit
    $hasil = [];
    foreach ($penyakit_list as $penyakit_id => $penyakit) {
        $probabilitas = $p; // P(vj) - Probabilitas awal penyakit
        
        foreach ($gejala_terpilih as $gejala_id) {
            // Mengecek apakah gejala terkait dengan penyakit
            $nc = isset($aturan_list[$penyakit_id][$gejala_id]) ? 1 : 0;
            
            // Hitung jumlah gejala yang terkait dengan penyakit
            $n = 1;

            // Hitung P(ai|vj) menggunakan rumus Naïve Bayes (smoothing)
            $p_ai_vj = (($nc + $total_gejala) * $p) / ($n + $total_gejala);
            echo "P($gejala_id|$penyakit_id) = ($nc + $total_gejala * $p) / ($n + $total_gejala) = $p_ai_vj<br>";
            
            // Perbarui probabilitas dengan mengalikan dengan probabilitas gejala
            $probabilitas *= $p_ai_vj;
        }
        
        // Simpan hasil perhitungan probabilitas untuk penyakit
        $hasil[$penyakit_id] = $probabilitas;
    }
    
    // Normalisasi: hitung jumlah total probabilitas
    $total_probabilitas = array_sum($hasil);
    
    if ($total_probabilitas > 0) {
        foreach ($hasil as $penyakit_id => $probabilitas) {
            // Normalisasi probabilitas dengan membagi dengan total probabilitas
            $hasil[$penyakit_id] = $probabilitas / $total_probabilitas;
        }
    }
    
    // Urutkan hasil dari probabilitas tertinggi
    arsort($hasil);
    
    // Siapkan data untuk penyakit lain
    $probabilitas_lain = [];
    foreach ($hasil as $penyakit_id => $prob) {
        if ($prob > 0) { 
            $probabilitas_lain[] = [
                'id' => $penyakit_id,
                'nama_penyakit' => $penyakit_list[$penyakit_id]['nama_penyakit'],
                'probabilitas' => $prob
            ];
        }
    }
    
    // Ambil penyakit dengan probabilitas tertinggi
    $penyakit_tertinggi_id = key($hasil);
    $penyakit_tertinggi = $penyakit_list[$penyakit_tertinggi_id];
    $penyakit_tertinggi['probabilitas'] = $hasil[$penyakit_tertinggi_id];
    
    // Tambahkan data probabilitas penyakit lain ke hasil
    $penyakit_tertinggi['probabilitas_lain'] = array_slice($probabilitas_lain, 1);
    
    return $penyakit_tertinggi;
}

// Fungsi lain yang diperlukan
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
?>
