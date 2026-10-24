<?php
class Barang
{
    // Property
    public $kode;
    public $nama;
    public $jumlah;

    // Constructor: dijalankan otomatis saat object dibuat
    public function __construct($kode, $nama, $jumlah)
    {
        $this->kode = $kode;
        $this->nama = $nama;
        $this->jumlah = $jumlah;
    }

    // Method: menampilkan format informasi barang
    public function tampilkanInfo()
    {
        return "[" . $this->kode . "] " . $this->nama . " - Jumlah: " . $this->jumlah . " unit";
    }
}