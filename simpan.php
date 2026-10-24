<?php
session_start();
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";
require_once "barang.php";

// Fungsi perhitungan total estimasi nilai aset (Jumlah x Harga Satuan)
function hitungTotalAset($jumlah, $harga)
{
    return $jumlah * $harga;
}

$daftarKondisi = ["Baik", "Rusak Ringan", "Rusak Berat"];
$error = [];
$ringkasan = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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

    $cek = $pdo->prepare("SELECT nama_kategori FROM kategori WHERE id = ?");
    $cek->execute([$idKategori]);
    $barisKategori = $cek->fetch();
    if ($barisKategori === false) {
        $error[] = "Kategori tidak valid.";
    }

    // ===== SIMPAN, LALU PINDAH KE HALAMAN RINGKASAN =====
    if (count($error) === 0) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO barang (kode_barang, nama_barang, id_kategori, jumlah, harga, kondisi)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$kode, $nama, $idKategori, $jumlah, $harga, $kondisi]);

            $barang = new Barang($kode, $nama, $jumlah);
            $_SESSION["ringkasan"] = [
                "kode"     => $kode,
                "nama"     => $nama,
                "kategori" => $barisKategori["nama_kategori"],
                "jumlah"   => $jumlah,
                "harga"    => $harga,
                "kondisi"  => $kondisi,
                "total"    => hitungTotalAset($jumlah, $harga),
                "info"     => $barang->tampilkanInfo(),
            ];

            // Pindah ke simpan.php lewat GET: menekan F5 tidak mengirim ulang form
            header("Location: simpan.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $error[] = "Kode barang " . $kode . " sudah terdaftar. Gunakan kode lain.";
            } else {
                $error[] = "Data gagal disimpan ke database.";
            }
        }
    }
} else {
    // Dibuka lewat GET: tampilkan ringkasan terakhir, atau kembali ke halaman utama
    $ringkasan = $_SESSION["ringkasan"] ?? null;
    if ($ringkasan === null) {
        header("Location: index.php");
        exit;
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

      <?php if ($ringkasan !== null): ?>
        <p class="pesan-sukses">Data barang berhasil disimpan ke database.</p>
        <p class="info-barang"><?= htmlspecialchars($ringkasan["info"]) ?></p>

        <div class="tabel-wrapper">
          <table>
            <caption>Ringkasan pemrosesan data barang</caption>
            <thead>
              <tr>
                <th scope="col">Kode</th>
                <th scope="col">Nama Barang</th>
                <th scope="col">Kategori</th>
                <th scope="col">Jumlah</th>
                <th scope="col">Harga Satuan</th>
                <th scope="col">Kondisi</th>
                <th scope="col">Total Estimasi Nilai Aset</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?= htmlspecialchars($ringkasan["kode"]) ?></td>
                <td><?= htmlspecialchars($ringkasan["nama"]) ?></td>
                <td><?= htmlspecialchars($ringkasan["kategori"]) ?></td>
                <td><?= (int) $ringkasan["jumlah"] ?></td>
                <td>Rp <?= number_format($ringkasan["harga"], 0, ",", ".") ?></td>
                <td><?= htmlspecialchars($ringkasan["kondisi"]) ?></td>
                <td><strong>Rp <?= number_format($ringkasan["total"], 0, ",", ".") ?></strong></td>
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
