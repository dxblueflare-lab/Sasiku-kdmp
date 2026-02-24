<?php
// invoice_generator.php
// File ini menghasilkan invoice dalam format PDF

// Ambil parameter dari URL
$order_id = $_GET['order_id'] ?? 'unknown';
$format = $_GET['format'] ?? 'pdf'; // Format bisa pdf, txt, atau csv

// Data pesanan (dalam implementasi nyata, ini akan diambil dari database)
$order_data = [
    'id' => $order_id,
    'tanggal' => date('d M Y'),
    'pelanggan' => 'Contoh Pelanggan',
    'alamat' => 'Jl. Contoh Alamat No. 123, Kota Contoh',
    'produk' => [
        ['nama' => 'Beras Premium', 'jumlah' => 2, 'harga' => 15000, 'subtotal' => 30000],
        ['nama' => 'Minyak Goreng', 'jumlah' => 1, 'harga' => 18000, 'subtotal' => 18000],
        ['nama' => 'Gula Pasir', 'jumlah' => 1, 'harga' => 12000, 'subtotal' => 12000]
    ],
    'total' => 60000,
    'status' => 'Diterima'
];

// Generate konten berdasarkan format
if ($format === 'pdf') {
    // Dalam implementasi nyata, kita akan menggunakan library seperti TCPDF atau DomPDF
    // Untuk simulasi, kita buat file PDF sederhana
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="invoice_' . $order_id . '.pdf"');
    
    // Konten PDF sederhana
    $pdf_content = "%PDF-1.4\n";
    $pdf_content .= "1 0 obj\n";
    $pdf_content .= "<<\n";
    $pdf_content .= "/Type /Catalog\n";
    $pdf_content .= "/Pages 2 0 R\n";
    $pdf_content .= ">>\n";
    $pdf_content .= "endobj\n";
    $pdf_content .= "2 0 obj\n";
    $pdf_content .= "<<\n";
    $pdf_content .= "/Type /Pages\n";
    $pdf_content .= "/Kids [3 0 R]\n";
    $pdf_content .= "/Count 1\n";
    $pdf_content .= ">>\n";
    $pdf_content .= "endobj\n";
    $pdf_content .= "3 0 obj\n";
    $pdf_content .= "<<\n";
    $pdf_content .= "/Type /Page\n";
    $pdf_content .= "/Parent 2 0 R\n";
    $pdf_content .= "/MediaBox [0 0 612 792]\n";
    $pdf_content .= "/Contents 4 0 R\n";
    $pdf_content .= ">>\n";
    $pdf_content .= "endobj\n";
    $pdf_content .= "4 0 obj\n";
    $pdf_content .= "<<\n";
    $pdf_content .= "/Length 100\n";
    $pdf_content .= ">>\n";
    $pdf_content .= "stream\n";
    $pdf_content .= "BT\n/F1 24 Tf\n72 720 Td\n(Invoice Pesanan " . $order_id . ") Tj\nET\n";
    $pdf_content .= "BT\n/F1 12 Tf\n72 680 Td\n(Tanggal: " . $order_data['tanggal'] . ") Tj\nET\n";
    $pdf_content .= "BT\n/F1 12 Tf\n72 660 Td\n(Pelanggan: " . $order_data['pelanggan'] . ") Tj\nET\n";
    $pdf_content .= "BT\n/F1 12 Tf\n72 640 Td\n(Alamat: " . $order_data['alamat'] . ") Tj\nET\n";
    $pdf_content .= "BT\n/F1 12 Tf\n72 600 Td\n(Total: Rp " . number_format($order_data['total']) . ") Tj\nET\n";
    $pdf_content .= "BT\n/F1 12 Tf\n72 580 Td\n(Status: " . $order_data['status'] . ") Tj\nET\n";
    $pdf_content .= "endstream\n";
    $pdf_content .= "endobj\n";
    $pdf_content .= "xref\n";
    $pdf_content .= "0 5\n";
    $pdf_content .= "0000000000 65535 f \n";
    $pdf_content .= "0000000010 00000 n \n";
    $pdf_content .= "0000000053 00000 n \n";
    $pdf_content .= "0000000136 00000 n \n";
    $pdf_content .= "0000000219 00000 n \n";
    $pdf_content .= "trailer\n";
    $pdf_content .= "<<\n";
    $pdf_content .= "/Size 5\n";
    $pdf_content .= "/Root 1 0 R\n";
    $pdf_content .= ">>\n";
    $pdf_content .= "%%EOF\n";
    
    echo $pdf_content;
} elseif ($format === 'csv') {
    // Generate file CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="invoice_' . $order_id . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Header CSV
    fputcsv($output, ['ID Pesanan', 'Tanggal', 'Pelanggan', 'Alamat', 'Produk', 'Jumlah', 'Harga', 'Subtotal', 'Total', 'Status']);
    
    // Data produk
    foreach ($order_data['produk'] as $produk) {
        fputcsv($output, [
            $order_data['id'],
            $order_data['tanggal'],
            $order_data['pelanggan'],
            $order_data['alamat'],
            $produk['nama'],
            $produk['jumlah'],
            $produk['harga'],
            $produk['subtotal'],
            $order_data['total'],
            $order_data['status']
        ]);
    }
    
    fclose($output);
} else {
    // Format default: TXT
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="invoice_' . $order_id . '.txt"');
    
    $invoice_content = "INVOICE PEMESANAN\n\n";
    $invoice_content .= "Nomor Invoice: INV-" . $order_id . "\n";
    $invoice_content .= "Tanggal: " . $order_data['tanggal'] . "\n";
    $invoice_content .= "Pelanggan: " . $order_data['pelanggan'] . "\n";
    $invoice_content .= "Alamat: " . $order_data['alamat'] . "\n\n";
    
    $invoice_content .= "Detail Pesanan:\n";
    foreach ($order_data['produk'] as $produk) {
        $invoice_content .= "- " . $produk['nama'] . " (" . $produk['jumlah'] . " x Rp " . number_format($produk['harga']) . ") = Rp " . number_format($produk['subtotal']) . "\n";
    }
    
    $invoice_content .= "\nTotal: Rp " . number_format($order_data['total']) . "\n";
    $invoice_content .= "Status: " . $order_data['status'] . "\n\n";
    $invoice_content .= "Terima kasih telah berbelanja di Dapur Suplai!";
    
    echo $invoice_content;
}
?>