<?php
session_start();
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";
require_once "barang.php";

function hitungTotalAset($jumlah, $harga)
{
    return $jumlah * $harga;
}

$daftarKondisi = ["Baik", "Rusak Ringan", "Rusak Berat"];
$error = [];

// Hanya proses kalau halaman dibuka lewat pengiriman form (POST)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$kode       = trim($_POST["kode"] ?? "");
$nama       = trim($_POST["nama"] ?? "");
$idKategori = (int) ($_POST["kategori"] ?? 0);
$jumlah     = (int) ($_POST["jumlah"] ?? 0);
$harga      = (float) ($_POST["harga"] ?? 0);
$kondisi    = trim($_POST["kondisi"] ?? "");

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

$cek = $pdo->prepare("SELECT id FROM kategori WHERE id = ?");
$cek->execute([$idKategori]);
if ($cek->fetch() === false) {
    $error[] = "Kategori tidak valid.";
}

if (count($error) === 0) {
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO barang (kode_barang, nama_barang, id_kategori, jumlah, harga, kondisi)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$kode, $nama, $idKategori, $jumlah, $harga, $kondisi]);

        // Simpan pesan sukses di session, lalu pindah ke halaman utama
        $barang = new Barang($kode, $nama, $jumlah);
        $_SESSION["flash"] = [
            "info"  => $barang->tampilkanInfo(),
            "total" => hitungTotalAset($jumlah, $harga),
        ];
        header("Location: index.php");
        exit;
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
      <h2>Data Tidak Dapat Diproses</h2>
      <ul class="daftar-error">
        <?php foreach ($error as $pesan): ?>
          <li><?= htmlspecialchars($pesan) ?></li>
        <?php endforeach; ?>
      </ul>
      <p class="kembali"><a href="index.php">&laquo; Kembali ke halaman utama</a></p>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 SIM-Barang | Praktikum Pemrograman Web | Karima Febriyanti (062530801584)</p>
  </footer>

</body>
</html>
