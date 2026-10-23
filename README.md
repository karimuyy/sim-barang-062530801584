# SIM-Barang

Sistem Pengelolaan Inventaris Barang Jurusan.

## Identitas
- Nama: Karima Febriyanti
- NIM: 062530801584
- Kelas: 3 IB
- Mata Kuliah: Praktikum Pemrograman Web
- Dosen Pengampu: Agus Setiawan, M.Kom.
- Program Studi: D3 Manajemen Informatika, Politeknik Negeri Sriwijaya

## Gambaran Proyek
SIM-Barang adalah aplikasi web untuk mencatat, mengelola, dan memantau
barang (aset) laboratorium dan jurusan. Aplikasi ini dikembangkan
bertahap dari Pertemuan 1 sampai 7 dalam praktikum Pemrograman Web.

## Teknologi yang Digunakan
- HTML5, CSS3, dan JavaScript (antarmuka dan interaksi)
- PHP native (pemrosesan di sisi server)
- MySQL (penyimpanan data)
- Laragon (server lokal), Git dan GitHub (version control)

## Rencana Pengembangan
| Pertemuan | Materi |
|---|---|
| 1 | Setup lingkungan dan Git |
| 2 | HTML5 semantik dan form |
| 3 | CSS3 responsif |
| 4 | JavaScript: validasi dan DOM |
| 5 | PHP: pemrosesan form |
| 6 | Login, session, dan OOP dasar |
| 7 | Database MySQL |

## Konsep HTTP
HTTP (HyperText Transfer Protocol) adalah aturan komunikasi antara
browser (client) dan server. Dengan aturan ini, browser dapat meminta
halaman web dan server dapat mengirimkannya.

### HTTP Request
HTTP Request adalah permintaan yang dikirim browser ke server. Isinya
antara lain method, URL, dan header. Method GET dipakai untuk meminta
data, misalnya saat membuka halaman SIM-Barang. Method POST dipakai
untuk mengirim data, misalnya saat mengisi form input barang lalu
menekan tombol Simpan.

### HTTP Response
HTTP Response adalah jawaban yang dikirim server ke browser. Isinya
antara lain status code, header, dan body (isi halaman, misalnya kode
HTML). Status code 200 berarti permintaan berhasil, sedangkan 404
berarti halaman yang diminta tidak ditemukan.

### Alur Singkat
1. Pengguna mengetik alamat di browser.
2. Browser mengirim HTTP Request ke server.
3. Server memproses permintaan tersebut.
4. Server mengirim HTTP Response.
5. Browser menampilkan halaman kepada pengguna.

### Contoh pada Proyek SIM-Barang
Saat membuka `http://localhost/sim-barang-062530801584/`, browser
mengirim Request GET ke server Laragon. Server lalu mengirim Response
berstatus 200 berisi halaman SIM-Barang.