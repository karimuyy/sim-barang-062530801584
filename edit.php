<?php
session_start();
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

$daftarKondisi = ["Baik", "Rusak Ringan", "Rusak Berat"];
$error = [];

// id diambil dari alamat (GET) saat halaman dibuka, atau dari form (POST) saat disimpan
$id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);

// Ambil data barang yang akan diubah
$stmt = $pdo->prepare(
    "SELECT id, kode_barang, nama_barang, id_kategori, jumlah, harga, kondisi
     FROM barang WHERE id = ?"
);
$stmt->execute([$id]);
$data = $stmt->fetch();

if ($data === false) {
    $_SESSION["pesan"] = "Barang tidak ditemukan.";
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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

    $cek = $pdo->prepare("SELECT id FROM kategori WHERE id = ?");
    $cek->execute([$idKategori]);
    if ($cek->fetch() === false) {
        $error[] = "Kategori tidak valid.";
    }

    // ===== SIMPAN PERUBAHAN =====
    if (count($error) === 0) {
        try {
            $ubah = $pdo->prepare(
                "UPDATE barang
                 SET kode_barang = ?, nama_barang = ?, id_kategori = ?,
                     jumlah = ?, harga = ?, kondisi = ?
                 WHERE id = ?"
            );
            $ubah->execute([$kode, $nama, $idKategori, $jumlah, $harga, $kondisi, $id]);

            $_SESSION["pesan"] = "Barang " . $nama . " berhasil diperbarui.";
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $error[] = "Kode barang " . $kode . " sudah dipakai barang lain.";
            } else {
                $error[] = "Perubahan gagal disimpan ke database.";
            }
        }
    }

    // Kalau ada error, form diisi kembali dengan data yang baru diketik
    $data = [
        "id"          => $id,
        "kode_barang" => $kode,
        "nama_barang" => $nama,
        "id_kategori" => $idKategori,
        "jumlah"      => $jumlah,
        "harga"       => $harga,
        "kondisi"     => $kondisi,
    ];
}

$daftarKategori = $pdo->query("SELECT id, nama_kategori FROM kategori ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ubah Barang | SIM-Barang</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <h1>SIM-Barang</h1>
    <p>Sistem Pengelolaan Inventaris Barang Jurusan</p>
  </header>

  <main>
    <section>
      <h2>Ubah Data Barang</h2>

      <?php if (count($error) > 0): ?>
        <p class="pesan-gagal">Perubahan tidak dapat disimpan:</p>
        <ul class="daftar-error">
          <?php foreach ($error as $pesan): ?>
            <li><?= htmlspecialchars($pesan) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <form action="edit.php" method="post">
        <input type="hidden" name="id" value="<?= (int) $data["id"] ?>">

        <div>
          <label for="kode">Kode Barang</label>
          <input type="text" id="kode" name="kode" value="<?= htmlspecialchars($data["kode_barang"]) ?>" required>
        </div>

        <div>
          <label for="nama">Nama Barang</label>
          <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data["nama_barang"]) ?>" required>
        </div>

        <div>
          <label for="kategori">Kategori</label>
          <select id="kategori" name="kategori" required>
            <?php foreach ($daftarKategori as $kat): ?>
              <option value="<?= (int) $kat["id"] ?>"
                <?= (int) $kat["id"] === (int) $data["id_kategori"] ? "selected" : "" ?>>
                <?= htmlspecialchars($kat["nama_kategori"]) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="jumlah">Jumlah</label>
          <input type="number" id="jumlah" name="jumlah" value="<?= (int) $data["jumlah"] ?>" required>
        </div>

        <div>
          <label for="harga">Harga Satuan (Rp)</label>
          <input type="number" id="harga" name="harga" min="0" value="<?= (float) $data["harga"] ?>" required>
        </div>

        <div>
          <label for="kondisi">Kondisi</label>
          <select id="kondisi" name="kondisi" required>
            <?php foreach ($daftarKondisi as $k): ?>
              <option value="<?= htmlspecialchars($k) ?>"
                <?= $k === $data["kondisi"] ? "selected" : "" ?>>
                <?= htmlspecialchars($k) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="aksi">
          <button type="submit">Simpan Perubahan</button>
          <a href="index.php" class="btn-edit btn-batal">Batal</a>
        </div>
      </form>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 SIM-Barang | Praktikum Pemrograman Web | Karima Febriyanti (062530801584)</p>
  </footer>

</body>
</html>