// download_functions.js
// File ini berisi fungsi-fungsi untuk download invoice dan laporan

/**
 * Fungsi untuk download invoice pesanan individual
 * @param {string} orderId - ID pesanan
 */
function downloadInvoice(orderId) {
    // Dalam implementasi nyata, ini akan mengunduh file invoice dari server
    // Untuk simulasi, kita buat file teks sederhana
    const invoiceContent = `INVOICE PEMESANAN\n\nNomor Invoice: INV-${orderId}\nTanggal: ${new Date().toLocaleDateString()}\nPelanggan: Contoh Pelanggan\n\nDetail Pesanan:\n- Produk: Contoh Produk\n- Jumlah: 1\n- Harga: Rp 100.000\n\nTotal: Rp 100.000\n\nTerima kasih telah berbelanja di Dapur Suplai!`;
    
    const blob = new Blob([invoiceContent], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `invoice_${orderId}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    alert(`Invoice untuk pesanan ${orderId} sedang diunduh...`);
}

/**
 * Fungsi untuk download semua pesanan dalam format Excel
 */
function downloadAllOrders() {
    // Redirect ke file PHP yang akan menghasilkan laporan Excel
    window.location.href = 'orders_report.php';
}

/**
 * Fungsi untuk download faktur pesanan dalam format PDF
 * @param {string} orderId - ID pesanan
 */
function downloadInvoicePDF(orderId) {
    // Dalam implementasi nyata, ini akan mengunduh file PDF dari server
    // Untuk simulasi, kita buat file PDF sederhana
    const pdfContent = `%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n>>\nendobj\n4 0 obj\n<<\n/Length 100\n>>\nstream\nBT\n/F1 24 Tf\n72 720 Td\n(Invoice Pesanan ${orderId}) Tj\nET\nBT\n/F1 12 Tf\n72 680 Td\n(Tanggal: ${new Date().toLocaleDateString()}) Tj\nET\nBT\n/F1 12 Tf\n72 660 Td\n(Pelanggan: Contoh Pelanggan) Tj\nET\nBT\n/F1 12 Tf\n72 640 Td\n(Alamat: Jl. Contoh No. 123) Tj\nET\nBT\n/F1 12 Tf\n72 600 Td\n(Total: Rp 100.000) Tj\nET\nBT\n/F1 12 Tf\n72 580 Td\n(Status: Lunas) Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000010 00000 n \n0000000053 00000 n \n0000000136 00000 n \n0000000219 00000 n \ntrailer\n<<\n/Size 5\n/Root 1 0 R\n>>\n%%EOF\n`;
    
    const blob = new Blob([pdfContent], { type: 'application/pdf' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `invoice_${orderId}.pdf`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    alert(`Invoice PDF untuk pesanan ${orderId} sedang diunduh...`);
}

/**
 * Fungsi untuk download laporan dalam format CSV
 */
function downloadReportCSV() {
    // Dalam implementasi nyata, ini akan mengunduh file CSV dari server
    // Untuk simulasi, kita buat file CSV sederhana
    const csvContent = "data:text/csv;charset=utf-8,ID Pesanan,Tanggal,Pelanggan,Produk,Jumlah,Total,Status\n" +
        "#ORD-001,15 Jan 2024,John Doe,Beras Premium,2 kg,Rp 30.000,Diterima\n" +
        "#ORD-002,14 Jan 2024,Jane Smith,Sayur Mayur Segar,1 paket,Rp 25.000,Diproses\n" +
        "#ORD-003,12 Jan 2024,Robert Johnson,Telur Ayam Segar,10 pcs,Rp 25.000,Dikonfirmasi\n" +
        "#ORD-004,10 Jan 2024,Emily Davis,Minyak Goreng,2 botol,Rp 36.000,Dikirim\n" +
        "#ORD-005,08 Jan 2024,Michael Wilson,Gula Pasir,1 kg,Rp 12.000,Pending";
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "laporan_pesanan_" + new Date().toISOString().split('T')[0] + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    alert('Laporan pesanan dalam format CSV sedang diunduh...');
}

/**
 * Fungsi untuk download nota pesanan
 * @param {string} orderId - ID pesanan
 */
function downloadReceipt(orderId) {
    // Dalam implementasi nyata, ini akan mengunduh file receipt dari server
    // Untuk simulasi, kita buat file teks sederhana
    const receiptContent = `NOTA PEMESANAN\n\nNomor Nota: NTN-${orderId}\nTanggal: ${new Date().toLocaleDateString()}\nWaktu: ${new Date().toLocaleTimeString()}\n\nDetail Pembelian:\n- Produk: Contoh Produk\n- Jumlah: 1\n- Harga: Rp 100.000\n\nTotal Pembayaran: Rp 100.000\n\nTerima kasih atas kunjungan Anda!`;
    
    const blob = new Blob([receiptContent], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `nota_${orderId}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    alert(`Nota untuk pesanan ${orderId} sedang diunduh...`);
}

// Ekspor fungsi-fungsi untuk digunakan di halaman lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        downloadInvoice,
        downloadAllOrders,
        downloadInvoicePDF,
        downloadReportCSV,
        downloadReceipt
    };
}