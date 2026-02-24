<?php
// orders_report.php
// File ini menghasilkan laporan pesanan dalam format Excel

// Dalam implementasi nyata, kita akan menggunakan library seperti PhpSpreadsheet
// Untuk simulasi, kita buat file CSV yang bisa dibuka di Excel

// Set header untuk download file
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_pesanan_' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Data pesanan (dalam implementasi nyata, ini akan diambil dari database)
$orders_data = [
    [
        'id' => 'ORD-001',
        'tanggal' => '15 Jan 2024',
        'pelanggan' => 'John Doe',
        'produk' => 'Beras Premium',
        'jumlah' => '2 kg',
        'total' => 'Rp 30.000',
        'status' => 'Diterima'
    ],
    [
        'id' => 'ORD-002',
        'tanggal' => '14 Jan 2024',
        'pelanggan' => 'Jane Smith',
        'produk' => 'Sayur Mayur Segar',
        'jumlah' => '1 paket',
        'total' => 'Rp 25.000',
        'status' => 'Diproses'
    ],
    [
        'id' => 'ORD-003',
        'tanggal' => '12 Jan 2024',
        'pelanggan' => 'Robert Johnson',
        'produk' => 'Telur Ayam Segar',
        'jumlah' => '10 pcs',
        'total' => 'Rp 25.000',
        'status' => 'Dikonfirmasi'
    ],
    [
        'id' => 'ORD-004',
        'tanggal' => '10 Jan 2024',
        'pelanggan' => 'Emily Davis',
        'produk' => 'Minyak Goreng',
        'jumlah' => '2 botol',
        'total' => 'Rp 36.000',
        'status' => 'Dikirim'
    ],
    [
        'id' => 'ORD-005',
        'tanggal' => '08 Jan 2024',
        'pelanggan' => 'Michael Wilson',
        'produk' => 'Gula Pasir',
        'jumlah' => '1 kg',
        'total' => 'Rp 12.000',
        'status' => 'Pending'
    ]
];

// Buat konten CSV
$output = fopen('php://output', 'w');

// Tulis header
fputcsv($output, ['ID Pesanan', 'Tanggal', 'Pelanggan', 'Produk', 'Jumlah', 'Total', 'Status'], "\t");

// Tulis data pesanan
foreach ($orders_data as $order) {
    fputcsv($output, [
        $order['id'],
        $order['tanggal'],
        $order['pelanggan'],
        $order['produk'],
        $order['jumlah'],
        $order['total'],
        $order['status']
    ], "\t");
}

fclose($output);
?>