<?php
session_start();
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";
require_once "Barang.php";

// ===== FUNGSI HITUNG TOTAL NILAI ASET =====
function hitungTotalAset($jumlah, $harga)
{
    return $jumlah * $harga;
}

$daftarKondisi = ["Baik", "Rusak Ringan", "Rusak Berat"];
$error = [];
$sukses = false;

// Hanya proses kalau halaman dibuka lewat pengiriman form (POST)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// ===== TANGKAP DATA DARI $_POST =====
$kode       = trim($_POST["kode"] ?? "");
$nama       = trim($_POST["nama"] ?? "");
$idKategori = (int) ($_POST["kategori"] ?? 0);
$jumlah     = (int) ($_POST["jumlah"] ?? 0);
$harga      = (float) ($_POST["harga"] ?? 0);
$kondisi    = trim($_POST["kondisi"] ?? "");

// ===== VALIDASI SISI SERVER =====
if (!preg_match('/^BRG-\d+$/', $kode)) {
    $error[] = "Kode barang harus diawali BRG- diikuti angka.";
}
if ($nama === "") {
    $error[] = "Nama barang tidak boleh kosong.";
}
if ($jumlah <= 0) {
    $error[] = "Jumlah harus lebih dari 0.";
}
if ($harga < 0) {
    $error[] = "Harga satuan tidak boleh negatif.";
}
if (!in_array($kondisi, $daftarKondisi, true)) {
    $error[] = "Kondisi tidak valid.";
}

// Pastikan kategori benar-benar ada di database
$namaKategori = "";
$cek = $pdo->prepare("SELECT nama_kategori FROM kategori WHERE id = ?");
$cek->execute([$idKategori]);
$barisKategori = $cek->fetch();
if ($barisKategori === false) {
    $error[] = "Kategori tidak valid.";
} else {
    $namaKategori = $barisKategori["nama_kategori"];
}

// ===== SIMPAN KE DATABASE =====
if (count($error) === 0) {
    try {
        $sql = "INSERT INTO barang (kode_barang, nama_barang, id_kategori, jumlah, harga, kondisi)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kode, $nama, $idKategori, $jumlah, $harga, $kondisi]);

        $sukses = true;
        $totalAset = hitungTotalAset($jumlah, $harga);
        $barang = new Barang($kode, $nama, $jumlah);
    } catch (PDOException $e) {
        if ($e->getCode() === "23000") {
            $error[] = "Kode barang " . $kode . " sudah terdaftar. Gunakan kode lain.";
        } else {
            $error[] = "Data gagal disimpan ke database.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Pemrosesan | SIM-Barang</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <h1>SIM-Barang</h1>
    <p>Sistem Pengelolaan Inventaris Barang Jurusan</p>
  </header>

  <main>
    <section>
      <h2>Hasil Pemrosesan Data</h2>

      <?php if ($sukses): ?>
        <p class="pesan-sukses">Data barang berhasil disimpan ke database.</p>
        <p class="info-barang"><?= htmlspecialchars($barang->tampilkanInfo()) ?></p>

        <div class="tabel-wrapper">
          <table>
            <caption>Ringkasan barang yang diinputkan</caption>
            <thead>
              <tr>
                <th scope="col">Kode</th>
                <th scope="col">Nama Barang</th>
                <th scope="col">Kategori</th>
                <th scope="col">Jumlah</th>
                <th scope="col">Harga Satuan</th>
                <th scope="col">Kondisi</th>
                <th scope="col">Total Nilai Aset</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?= htmlspecialchars($kode) ?></td>
                <td><?= htmlspecialchars($nama) ?></td>
                <td><?= htmlspecialchars($namaKategori) ?></td>
                <td><?= $jumlah ?></td>
                <td>Rp <?= number_format($harga, 0, ",", ".") ?></td>
                <td><?= htmlspecialchars($kondisi) ?></td>
                <td><strong>Rp <?= number_format($totalAset, 0, ",", ".") ?></strong></td>
              </tr>
            </tbody>
          </table>
        </div>

      <?php else: ?>
        <p class="pesan-gagal">Data tidak dapat diproses:</p>
        <ul class="daftar-error">
          <?php foreach ($error as $pesan): ?>
            <li><?= htmlspecialchars($pesan) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <p class="kembali"><a href="index.php">&laquo; Kembali ke halaman utama</a></p>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 SIM-Barang | Praktikum Pemrograman Web | Karima Febriyanti (062530801584)</p>
  </footer>

</body>
</html>