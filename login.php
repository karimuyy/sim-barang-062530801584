<?php
session_start();

// Kalau sudah login, tidak perlu melihat halaman login lagi
if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    header("Location: index.php");
    exit;
}

// Data pengguna disimpan di array asosiatif (username => password)
$users = [
    "admin"   => "password123",
    "petugas" => "petugas456",
];

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (isset($users[$username]) && $users[$username] === $password) {
        session_regenerate_id(true);       // ganti ID session demi keamanan
        $_SESSION["login"] = true;
        $_SESSION["username"] = $username;
        header("Location: index.php");
        exit;
    }

    $error = "Username atau password salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | SIM-Barang</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <h1>SIM-Barang</h1>
    <p>Sistem Pengelolaan Inventaris Barang Jurusan</p>
  </header>

  <main class="halaman-login">
    <section>
      <h2>Login</h2>

      <?php if ($error !== ""): ?>
        <p class="pesan-gagal"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form action="login.php" method="post">
        <div>
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Masukkan username" required>
        </div>

        <div>
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Masukkan password" required>
        </div>

        <div class="aksi">
          <button type="submit">Masuk</button>
        </div>
      </form>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 SIM-Barang | Praktikum Pemrograman Web | Karima Febriyanti (062530801584)</p>
  </footer>

</body>
</html>