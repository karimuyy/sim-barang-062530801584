// ===== AMBIL ELEMEN DARI HALAMAN =====
const form = document.querySelector("form");
const inputKode = document.getElementById("kode");
const inputJumlah = document.getElementById("jumlah");
const errorKode = document.getElementById("error-kode");
const errorJumlah = document.getElementById("error-jumlah");
const inputCari = document.getElementById("cari");
const toast = document.getElementById("toast");

// ===== FUNGSI BANTU: TAMPIL / HAPUS PESAN ERROR =====
function tampilkanError(input, span, pesan) {
  span.textContent = pesan;
  input.classList.add("invalid");
}

function hapusError(input, span) {
  span.textContent = "";
  input.classList.remove("invalid");
}

// ===== FITUR TOAST =====
let timerToast;

function tampilkanToast(pesan) {
  toast.textContent = pesan;
  toast.classList.add("show");

  clearTimeout(timerToast);
  timerToast = setTimeout(function () {
    toast.classList.remove("show");
  }, 3000);
}

// ===== VALIDASI FORM SAAT SUBMIT =====
form.addEventListener("submit", function (e) {
  // Tahan dulu: periksa isian, dan beri waktu agar toast terlihat
  e.preventDefault();

  let valid = true;
  const kode = inputKode.value.trim();
  const jumlah = Number(inputJumlah.value);

  // Aturan 1: kode harus "BRG-" diikuti angka
  const polaKode = /^BRG-\d+$/;
  if (!polaKode.test(kode)) {
    tampilkanError(inputKode, errorKode, "Kode harus diawali BRG- diikuti angka, contoh: BRG-001");
    valid = false;
  } else {
    hapusError(inputKode, errorKode);
  }

  // Aturan 2: jumlah harus angka lebih dari 0
  if (inputJumlah.value.trim() === "" || isNaN(jumlah) || jumlah <= 0) {
    tampilkanError(inputJumlah, errorJumlah, "Jumlah harus berupa angka lebih dari 0");
    valid = false;
  } else {
    hapusError(inputJumlah, errorJumlah);
  }

  // Jika ada yang salah, berhenti di sini (form tidak dikirim)
  if (!valid) {
    return;
  }

  // Jika semua benar: tampilkan toast, lalu kirim form ke simpan.php
  tampilkanToast("Data valid, sedang dikirim...");
  setTimeout(function () {
    form.submit();
  }, 1000);
});

// Pesan error hilang saat pengguna mulai memperbaiki isian
inputKode.addEventListener("input", function () {
  hapusError(inputKode, errorKode);
});

inputJumlah.addEventListener("input", function () {
  hapusError(inputJumlah, errorJumlah);
});

// ===== FITUR PENCARIAN TABEL REAL-TIME =====
inputCari.addEventListener("input", function () {
  const kataKunci = inputCari.value.toLowerCase().trim();
  const semuaBaris = document.querySelectorAll("tbody tr");

  semuaBaris.forEach(function (baris) {
    const teksBaris = baris.textContent.toLowerCase();
    if (teksBaris.includes(kataKunci)) {
      baris.style.display = "";      // tampilkan
    } else {
      baris.style.display = "none";  // sembunyikan
    }
  });
});