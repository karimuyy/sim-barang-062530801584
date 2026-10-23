<?php
session_start();
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}
require_once "barang.php";
// ===== FUNGSI HITUNG TOTAL NILAI ASET =====
function hitungTotalAset($jumlah, $harga)
{
    return $jumlah * $harga;
}

// ===== DAFTAR KATEGORI (sama dengan value di dropdown) =====
$daftarKategori = [
    1 => "Elektronik",
    2 => "Furnitur",
    3 => "Alat Tulis",
];

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
if (!isset($daftarKategori[$idKategori])) {
    $error[] = "Kategori tidak valid.";
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

if (count($error) === 0) {
    $sukses = true;
    $namaKategori = $daftarKategori[$idKategori];
    $totalAset = hitungTotalAset($jumlah, $harga);

    $barang = new Barang($kode, $nama, $jumlah);   // BARU
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
        <p class="pesan-sukses">Data barang berhasil diterima oleh server.</p>
        <p class="info-barang"><?= htmlspecialchars($barang->tampilkanInfo()) ?></p>

        <div class="tabel-wrapper">
          <table>
            <caption>Ringkasan barang yang diinputkan</caption>
            <tbody>
              <tr>
                <th scope="row">Kode Barang</th>
                <td><?= htmlspecialchars($kode) ?></td>
              </tr>
              <tr>
                <th scope="row">Nama Barang</th>
                <td><?= htmlspecialchars($nama) ?></td>
              </tr>
              <tr>
                <th scope="row">Kategori</th>
                <td><?= htmlspecialchars($namaKategori) ?></td>
              </tr>
              <tr>
                <th scope="row">Jumlah</th>
                <td><?= $jumlah ?> unit</td>
              </tr>
              <tr>
                <th scope="row">Harga Satuan</th>
                <td>Rp <?= number_format($harga, 0, ",", ".") ?></td>
              </tr>
              <tr>
                <th scope="row">Kondisi</th>
                <td><?= htmlspecialchars($kondisi) ?></td>
              </tr>
              <tr>
                <th scope="row">Total Estimasi Nilai Aset</th>
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