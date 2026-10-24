<?php
session_start();
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

// Ringkasan input terakhir sudah tidak diperlukan setelah kembali ke halaman utama
unset($_SESSION["ringkasan"]);

// Pesan singkat (misalnya setelah menghapus), tampil sekali saja
$pesan = $_SESSION["pesan"] ?? null;
unset($_SESSION["pesan"]);

// BARU: b.id ikut diambil, dipakai oleh tombol Hapus
$stmt = $pdo->query(
    "SELECT b.id, b.kode_barang, b.nama_barang, k.nama_kategori, b.jumlah, b.kondisi
     FROM barang b
     JOIN kategori k ON b.id_kategori = k.id
     ORDER BY b.id ASC"
);
$daftarBarang = $stmt->fetchAll();

$daftarKategori = $pdo->query("SELECT id, nama_kategori FROM kategori ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIM-Barang | Inventaris Barang Jurusan</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <h1>SIM-Barang</h1>
    <p>Sistem Pengelolaan Inventaris Barang Jurusan</p>
  </header>

  <nav>
    <ul>
      <li><a href="#inventaris">Data Inventaris</a></li>
      <li><a href="#form-barang">Tambah Barang</a></li>
      <li><a href="logout.php">Logout (<?= htmlspecialchars($_SESSION["username"]) ?>)</a></li>
    </ul>
  </nav>

  <main>

    <?php if ($pesan): ?>
      <p class="pesan-sukses"><?= htmlspecialchars($pesan) ?></p>
    <?php endif; ?>

    <section id="inventaris">
      <h2>Tabel Inventaris Barang</h2>

      <div class="pencarian">
        <label for="cari">Cari barang</label>
        <input type="search" id="cari" placeholder="Ketik kode, nama, kategori, atau kondisi...">
      </div>

      <div class="tabel-wrapper">
        <table>
          <caption>Daftar barang laboratorium dan jurusan</caption>
          <thead>
            <tr>
              <th scope="col">Kode</th>
              <th scope="col">Nama Barang</th>
              <th scope="col">Kategori</th>
              <th scope="col">Jumlah</th>
              <th scope="col">Kondisi</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($daftarBarang as $barang): ?>
              <tr>
                <td><?= htmlspecialchars($barang["kode_barang"]) ?></td>
                <td><?= htmlspecialchars($barang["nama_barang"]) ?></td>
                <td><?= htmlspecialchars($barang["nama_kategori"]) ?></td>
                <td><?= (int) $barang["jumlah"] ?></td>
                <td><?= htmlspecialchars($barang["kondisi"]) ?></td>
                <td>
                  <div class="aksi-baris">
                    <a href="edit.php?id=<?= (int) $barang["id"] ?>" class="btn-edit">Edit</a>
                    <form class="form-hapus" action="hapus.php" method="post"
                        onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                      <input type="hidden" name="id" value="<?= (int) $barang["id"] ?>">
                      <button type="submit" class="btn-hapus">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section id="form-barang">
      <h2>Formulir Input Barang</h2>
      <form action="simpan.php" method="post">

        <div>
          <label for="kode">Kode Barang</label>
          <input type="text" id="kode" name="kode" placeholder="Contoh: BRG-006" required>
          <span class="error" id="error-kode"></span>
        </div>

        <div>
          <label for="nama">Nama Barang</label>
          <input type="text" id="nama" name="nama" placeholder="Contoh: Printer" required>
        </div>

        <div>
          <label for="kategori">Kategori</label>
          <select id="kategori" name="kategori" required>
            <option value="">-- Pilih kategori --</option>
            <?php foreach ($daftarKategori as $kat): ?>
              <option value="<?= (int) $kat["id"] ?>"><?= htmlspecialchars($kat["nama_kategori"]) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="jumlah">Jumlah</label>
          <input type="number" id="jumlah" name="jumlah" placeholder="Contoh: 10" required>
          <span class="error" id="error-jumlah"></span>
        </div>

        <div>
          <label for="harga">Harga Satuan (Rp)</label>
          <input type="number" id="harga" name="harga" min="0" placeholder="Contoh: 1500000" required>
        </div>

        <div>
          <label for="kondisi">Kondisi</label>
          <select id="kondisi" name="kondisi" required>
            <option value="">-- Pilih kondisi --</option>
            <option value="Baik">Baik</option>
            <option value="Rusak Ringan">Rusak Ringan</option>
            <option value="Rusak Berat">Rusak Berat</option>
          </select>
        </div>

        <div class="aksi">
          <button type="submit">Simpan Barang</button>
          <button type="reset">Reset</button>
        </div>

      </form>
    </section>

  </main>

  <footer>
    <p>&copy; 2026 SIM-Barang | Praktikum Pemrograman Web | Karima Febriyanti (062530801584)</p>
  </footer>

  <div id="toast" class="toast" role="status"></div>

  <script src="main.js"></script>
</body>
</html>
