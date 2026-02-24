<?php
// reports/generate_excel_report.php
// File ini menghasilkan laporan dalam format Excel untuk sistem Dapur Suplai

require_once '../config/database.php';

// Dalam implementasi nyata, kita akan menggunakan PhpSpreadsheet
// Untuk simulasi, kita buat file CSV yang bisa dibuka di Excel

// Dapatkan parameter dari URL
$report_type = $_GET['type'] ?? 'orders';
$role = $_GET['role'] ?? 'admin'; // Parameter role untuk menentukan akses
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Cek apakah pengguna sudah login dan memiliki akses sesuai role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $role) {
    // Jika tidak memiliki akses, redirect ke halaman login
    header('Location: ../auth/login.html');
    exit();
}

// Dapatkan data berdasarkan jenis laporan dan role
switch($report_type) {
    case 'orders':
        $filename = 'laporan_pesanan_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = ['ID Pesanan', 'Tanggal', 'Pelanggan', 'Produk', 'Jumlah', 'Total', 'Status', 'Supplier'];
        
        // Dalam implementasi nyata, ini akan diambil dari database
        // Untuk simulasi, kita buat data dummy
        $data = [
            ['ORD-001', '2024-01-15', 'John Doe', 'Beras Premium', '2 kg', 'Rp 30.000', 'Selesai', 'CV. Sumber Rejeki'],
            ['ORD-002', '2024-01-14', 'Jane Smith', 'Sayur Mayur Segar', '1 paket', 'Rp 25.000', 'Diproses', 'UD. Segar Tani'],
            ['ORD-003', '2024-01-12', 'Robert Johnson', 'Telur Ayam Segar', '10 pcs', 'Rp 25.000', 'Dikirim', 'PT. Peternakan Sejahtera'],
            ['ORD-004', '2024-01-10', 'Emily Davis', 'Minyak Goreng', '2 botol', 'Rp 36.000', 'Diterima', 'CV. Minyak Jaya'],
            ['ORD-005', '2024-01-08', 'Michael Wilson', 'Gula Pasir', '1 kg', 'Rp 12.000', 'Pending', 'UD. Gula Manis']
        ];
        break;
    case 'products':
        $filename = 'laporan_produk_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = ['ID Produk', 'Nama Produk', 'Kategori', 'Harga', 'Stok', 'Supplier', 'Status'];
        $data = [
            ['PROD-001', 'Beras Premium', 'Sembako', 'Rp 15.000/kg', '120', 'CV. Sumber Rejeki', 'Aktif'],
            ['PROD-002', 'Sayur Mayur Segar', 'Sayur-Sayuran', 'Rp 25.000/paket', '85', 'UD. Segar Tani', 'Aktif'],
            ['PROD-003', 'Telur Ayam Segar', 'Protein Hewani', 'Rp 2.500/pcs', '200', 'PT. Peternakan Sejahtera', 'Aktif'],
            ['PROD-004', 'Minyak Goreng', 'Sembako', 'Rp 18.000/botol', '60', 'CV. Minyak Jaya', 'Tidak Aktif'],
            ['PROD-005', 'Gula Pasir', 'Sembako', 'Rp 12.000/kg', '150', 'UD. Gula Manis', 'Aktif']
        ];
        break;
    case 'customers':
        $filename = 'laporan_pelanggan_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = ['ID Pelanggan', 'Nama', 'Email', 'Telepon', 'Alamat', 'Tanggal Registrasi', 'Status'];
        $data = [
            ['CUST-001', 'John Doe', 'john@example.com', '+6281234567890', 'Jl. Contoh No. 123', '2024-01-01', 'Aktif'],
            ['CUST-002', 'Jane Smith', 'jane@example.com', '+6281234567891', 'Jl. Contoh No. 124', '2024-01-02', 'Aktif'],
            ['CUST-003', 'Robert Johnson', 'robert@example.com', '+6281234567892', 'Jl. Contoh No. 125', '2024-01-03', 'Non-Aktif'],
            ['CUST-004', 'Emily Davis', 'emily@example.com', '+6281234567893', 'Jl. Contoh No. 126', '2024-01-04', 'Aktif'],
            ['CUST-005', 'Michael Wilson', 'michael@example.com', '+6281234567894', 'Jl. Contoh No. 127', '2024-01-05', 'Aktif']
        ];
        break;
    case 'revenue':
        $filename = 'laporan_pendapatan_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = ['Periode', 'Jumlah Pesanan', 'Pendapatan Bruto', 'Biaya Operasional', 'Laba Bersih', 'Margin (%)'];
        $data = [
            ['Januari 2024', '124', 'Rp 42.500.000', 'Rp 12.750.000', 'Rp 29.750.000', '70%'],
            ['Desember 2023', '118', 'Rp 38.200.000', 'Rp 11.460.000', 'Rp 26.740.000', '70%'],
            ['November 2023', '132', 'Rp 45.600.000', 'Rp 13.680.000', 'Rp 31.920.000', '70%'],
            ['Oktober 2023', '109', 'Rp 35.800.000', 'Rp 10.740.000', 'Rp 25.060.000', '70%'],
            ['September 2023', '98', 'Rp 32.400.000', 'Rp 9.720.000', 'Rp 22.680.000', '70%']
        ];
        break;
    case 'inventory':
        $filename = 'laporan_stok_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = ['ID Produk', 'Nama Produk', 'Stok Tersedia', 'Stok Minimum', 'Status', 'Tanggal Update'];
        $data = [
            ['PROD-001', 'Beras Premium', '120', '10', 'Aman', '2024-01-15'],
            ['PROD-002', 'Sayur Mayur Segar', '85', '5', 'Aman', '2024-01-15'],
            ['PROD-003', 'Telur Ayam Segar', '200', '20', 'Aman', '2024-01-15'],
            ['PROD-004', 'Minyak Goreng', '3', '10', 'Rendah', '2024-01-15'],
            ['PROD-005', 'Gula Pasir', '150', '15', 'Aman', '2024-01-15']
        ];
        break;
    case 'supplier_orders':
        $filename = 'laporan_pesanan_supplier_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = ['ID Pesanan', 'Tanggal', 'Produk', 'Jumlah', 'Total', 'Status', 'Pelanggan'];
        $data = [
            ['ORD-001', '2024-01-15', 'Beras Premium', '2 kg', 'Rp 30.000', 'Selesai', 'John Doe'],
            ['ORD-003', '2024-01-12', 'Telur Ayam Segar', '10 pcs', 'Rp 25.000', 'Dikirim', 'Robert Johnson'],
            ['ORD-005', '2024-01-08', 'Gula Pasir', '1 kg', 'Rp 12.000', 'Pending', 'Michael Wilson']
        ];
        break;
    default:
        $filename = 'laporan_umum_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = ['ID', 'Judul', 'Nilai'];
        $data = [
            ['1', 'Total Pesanan', '1,248'],
            ['2', 'Total Produk', '542'],
            ['3', 'Total Pelanggan', '1,248'],
            ['4', 'Pendapatan Bulan Ini', 'Rp 42.500.000'],
            ['5', 'Pesanan Pending', '42']
        ];
}

// Set header untuk download file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// Tulis header
fputcsv($output, $headers);

// Tulis data
foreach ($data as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>