<?php
// models/ReportModel.php
// Model untuk mengelola laporan dan analitik dalam sistem Dapur Suplai

class ReportModel {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    // Mendapatkan laporan penjualan harian
    public function getDailySalesReport($date) {
        $query = "SELECT 
                    COUNT(p.id) as total_orders,
                    SUM(p.total_harga) as total_revenue,
                    AVG(p.total_harga) as avg_order_value,
                    COUNT(CASE WHEN p.status_pesanan = 'delivered' THEN 1 END) as delivered_orders
                  FROM pesanan p
                  WHERE DATE(p.tanggal_pesanan) = :date";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan laporan penjualan mingguan
    public function getWeeklySalesReport($startDate, $endDate) {
        $query = "SELECT 
                    COUNT(p.id) as total_orders,
                    SUM(p.total_harga) as total_revenue,
                    AVG(p.total_harga) as avg_order_value,
                    COUNT(CASE WHEN p.status_pesanan = 'delivered' THEN 1 END) as delivered_orders
                  FROM pesanan p
                  WHERE p.tanggal_pesanan BETWEEN :start_date AND :end_date";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan laporan penjualan bulanan
    public function getMonthlySalesReport($month, $year) {
        $query = "SELECT 
                    COUNT(p.id) as total_orders,
                    SUM(p.total_harga) as total_revenue,
                    AVG(p.total_harga) as avg_order_value,
                    COUNT(CASE WHEN p.status_pesanan = 'delivered' THEN 1 END) as delivered_orders
                  FROM pesanan p
                  WHERE MONTH(p.tanggal_pesanan) = :month AND YEAR(p.tanggal_pesanan) = :year";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan laporan penjualan tahunan
    public function getAnnualSalesReport($year) {
        $query = "SELECT 
                    COUNT(p.id) as total_orders,
                    SUM(p.total_harga) as total_revenue,
                    AVG(p.total_harga) as avg_order_value,
                    COUNT(CASE WHEN p.status_pesanan = 'delivered' THEN 1 END) as delivered_orders
                  FROM pesanan p
                  WHERE YEAR(p.tanggal_pesanan) = :year";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan laporan penjualan per produk
    public function getSalesByProduct($startDate, $endDate) {
        $query = "SELECT 
                    pr.nama_produk,
                    pr.harga,
                    SUM(dp.jumlah) as total_sold,
                    SUM(dp.subtotal) as total_revenue,
                    pr.stok
                  FROM detail_pesanan dp
                  JOIN produk pr ON dp.id_produk = pr.id
                  JOIN pesanan p ON dp.id_pesanan = p.id
                  WHERE p.tanggal_pesanan BETWEEN :start_date AND :end_date
                  GROUP BY pr.id
                  ORDER BY total_sold DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan laporan penjualan per kategori
    public function getSalesByCategory($startDate, $endDate) {
        $query = "SELECT 
                    k.nama_kategori,
                    COUNT(dp.id) as total_items_sold,
                    SUM(dp.jumlah) as total_quantity,
                    SUM(dp.subtotal) as total_revenue
                  FROM detail_pesanan dp
                  JOIN produk p ON dp.id_produk = p.id
                  JOIN kategori_produk k ON p.id_kategori = k.id
                  JOIN pesanan o ON dp.id_pesanan = o.id
                  WHERE o.tanggal_pesanan BETWEEN :start_date AND :end_date
                  GROUP BY k.id
                  ORDER BY total_revenue DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan laporan penjualan per supplier
    public function getSalesBySupplier($startDate, $endDate) {
        $query = "SELECT 
                    u.nama_lengkap as supplier_name,
                    COUNT(DISTINCT p.id) as total_orders,
                    SUM(dp.jumlah) as total_products_sold,
                    SUM(dp.subtotal) as total_revenue
                  FROM detail_pesanan dp
                  JOIN produk pr ON dp.id_produk = pr.id
                  JOIN users u ON pr.id_supplier = u.id
                  JOIN pesanan p ON dp.id_pesanan = p.id
                  WHERE p.tanggal_pesanan BETWEEN :start_date AND :end_date
                  GROUP BY u.id
                  ORDER BY total_revenue DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan laporan stok rendah
    public function getLowStockReport($threshold = 10) {
        $query = "SELECT 
                    p.nama_produk,
                    p.stok,
                    k.nama_kategori,
                    u.nama_lengkap as supplier_name
                  FROM produk p
                  JOIN kategori_produk k ON p.id_kategori = k.id
                  JOIN users u ON p.id_supplier = u.id
                  WHERE p.stok <= :threshold
                  ORDER BY p.stok ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':threshold', $threshold);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan laporan aktivitas pengguna
    public function getUserActivityReport($startDate, $endDate) {
        $query = "SELECT 
                    u.nama_lengkap,
                    u.email,
                    COUNT(p.id) as total_orders,
                    SUM(p.total_harga) as total_spent,
                    MAX(p.tanggal_pesanan) as last_order_date
                  FROM users u
                  LEFT JOIN pesanan p ON u.id = p.id_user
                  WHERE u.created_at BETWEEN :start_date AND :end_date
                  GROUP BY u.id
                  ORDER BY total_spent DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan data untuk grafik penjualan
    public function getSalesChartData($period = 'monthly', $limit = 12) {
        switch($period) {
            case 'daily':
                $query = "SELECT 
                            DATE(tanggal_pesanan) as period,
                            COUNT(id) as order_count,
                            SUM(total_harga) as revenue
                          FROM pesanan
                          WHERE tanggal_pesanan >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                          GROUP BY DATE(tanggal_pesanan)
                          ORDER BY period DESC
                          LIMIT :limit";
                break;
            case 'weekly':
                $query = "SELECT 
                            YEARWEEK(tanggal_pesanan) as period,
                            COUNT(id) as order_count,
                            SUM(total_harga) as revenue
                          FROM pesanan
                          WHERE tanggal_pesanan >= DATE_SUB(NOW(), INTERVAL 52 WEEK)
                          GROUP BY YEARWEEK(tanggal_pesanan)
                          ORDER BY period DESC
                          LIMIT :limit";
                break;
            case 'monthly':
            default:
                $query = "SELECT 
                            CONCAT(YEAR(tanggal_pesanan), '-', LPAD(MONTH(tanggal_pesanan), 2, '0')) as period,
                            COUNT(id) as order_count,
                            SUM(total_harga) as revenue
                          FROM pesanan
                          WHERE tanggal_pesanan >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                          GROUP BY YEAR(tanggal_pesanan), MONTH(tanggal_pesanan)
                          ORDER BY period DESC
                          LIMIT :limit";
                break;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan ringkasan laporan untuk dashboard
    public function getDashboardSummary() {
        $summary = [];
        
        // Total pesanan
        $query = "SELECT COUNT(*) as total FROM pesanan";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $summary['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total pendapatan
        $query = "SELECT COALESCE(SUM(total_harga), 0) as total FROM pesanan WHERE status_pesanan IN ('confirmed', 'processing', 'shipped', 'delivered')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $summary['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Pesanan pending
        $query = "SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $summary['pending_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total produk
        $query = "SELECT COUNT(*) as total FROM produk";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $summary['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total pengguna
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $summary['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return $summary;
    }
    
    // Mendapatkan laporan penjualan terbaru
    public function getRecentSales($limit = 5) {
        $query = "SELECT 
                    p.id,
                    p.kode_pesanan,
                    p.total_harga,
                    p.status_pesanan,
                    p.tanggal_pesanan,
                    u.nama_lengkap as customer_name
                  FROM pesanan p
                  JOIN users u ON p.id_user = u.id
                  ORDER BY p.tanggal_pesanan DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan produk terlaris
    public function getBestSellingProducts($limit = 10) {
        $query = "SELECT 
                    pr.nama_produk,
                    pr.harga,
                    SUM(dp.jumlah) as total_sold,
                    SUM(dp.subtotal) as total_revenue,
                    pr.stok
                  FROM detail_pesanan dp
                  JOIN produk pr ON dp.id_produk = pr.id
                  GROUP BY pr.id
                  ORDER BY total_sold DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Fungsi helper untuk laporan
function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function format_date_range($startDate, $endDate) {
    $start = date('d M Y', strtotime($startDate));
    $end = date('d M Y', strtotime($endDate));
    return "$start - $end";
}

function get_status_badge_class($status) {
    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-blue-100 text-blue-800',
        'processing' => 'bg-purple-100 text-purple-800',
        'shipped' => 'bg-indigo-100 text-indigo-800',
        'delivered' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800'
    ];
    
    return $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
}

function get_period_label($period, $type = 'monthly') {
    switch($type) {
        case 'daily':
            return date('d M Y', strtotime($period));
        case 'weekly':
            return 'Week ' . substr($period, -2) . ' of ' . substr($period, 0, 4);
        case 'monthly':
        default:
            $parts = explode('-', $period);
            $months = [
                '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
                '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
            ];
            return $months[$parts[1]] . ' ' . $parts[0];
    }
}
?>