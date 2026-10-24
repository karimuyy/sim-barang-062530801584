<?php
session_start();
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}

// Penghapusan hanya diterima lewat POST (dari tombol di tabel)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

require_once "koneksi.php";

$id = (int) ($_POST["id"] ?? 0);

if ($id > 0) {
    $cek = $pdo->prepare("SELECT nama_barang FROM barang WHERE id = ?");
    $cek->execute([$id]);
    $barang = $cek->fetch();

    if ($barang) {
        $hapus = $pdo->prepare("DELETE FROM barang WHERE id = ?");
        $hapus->execute([$id]);
        $_SESSION["pesan"] = "Barang " . $barang["nama_barang"] . " berhasil dihapus.";
    } else {
        $_SESSION["pesan"] = "Barang tidak ditemukan.";
    }
}

header("Location: index.php");
exit;
