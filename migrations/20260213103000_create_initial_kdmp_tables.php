<?php
/**
 * Migration: create_initial_kdmp_tables
 * Created: 2026-02-13 10:30:00
 */

function create_initial_kdmp_tables($db) {
    try {
        // Create users table
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'customer',
            nama_lengkap TEXT,
            alamat TEXT,
            nomor_telepon TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->query($sql);

        // Create kategori_produk table
        $sql = "
        CREATE TABLE IF NOT EXISTS kategori_produk (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nama_kategori TEXT NOT NULL,
            deskripsi TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->query($sql);

        // Create produk table
        $sql = "
        CREATE TABLE IF NOT EXISTS produk (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nama_produk TEXT NOT NULL,
            deskripsi TEXT,
            harga REAL NOT NULL,
            stok INTEGER NOT NULL DEFAULT 0,
            id_kategori INTEGER,
            gambar_url TEXT,
            berat REAL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_kategori) REFERENCES kategori_produk(id)
        )";
        $db->query($sql);

        // Create pesanan table
        $sql = "
        CREATE TABLE IF NOT EXISTS pesanan (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_user INTEGER NOT NULL,
            total_harga REAL NOT NULL,
            status_pesanan TEXT NOT NULL DEFAULT 'pending',
            metode_pembayaran TEXT,
            alamat_pengiriman TEXT,
            catatan TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_user) REFERENCES users(id)
        )";
        $db->query($sql);

        // Create detail_pesanan table
        $sql = "
        CREATE TABLE IF NOT EXISTS detail_pesanan (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_pesanan INTEGER NOT NULL,
            id_produk INTEGER NOT NULL,
            jumlah INTEGER NOT NULL,
            harga_satuan REAL NOT NULL,
            subtotal REAL NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_pesanan) REFERENCES pesanan(id),
            FOREIGN KEY (id_produk) REFERENCES produk(id)
        )";
        $db->query($sql);

        // Create keranjang table
        $sql = "
        CREATE TABLE IF NOT EXISTS keranjang (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_user INTEGER NOT NULL,
            id_produk INTEGER NOT NULL,
            jumlah INTEGER NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_user) REFERENCES users(id),
            FOREIGN KEY (id_produk) REFERENCES produk(id),
            UNIQUE(id_user, id_produk)
        )";
        $db->query($sql);

        // Create pelacakan_pesanan table
        $sql = "
        CREATE TABLE IF NOT EXISTS pelacakan_pesanan (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_pesanan INTEGER NOT NULL,
            status_pelacakan TEXT NOT NULL,
            catatan TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_pesanan) REFERENCES pesanan(id)
        )";
        $db->query($sql);

        // Create ulasan_produk table
        $sql = "
        CREATE TABLE IF NOT EXISTS ulasan_produk (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_produk INTEGER NOT NULL,
            id_user INTEGER NOT NULL,
            rating INTEGER CHECK(rating >= 1 AND rating <= 5),
            komentar TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_produk) REFERENCES produk(id),
            FOREIGN KEY (id_user) REFERENCES users(id)
        )";
        $db->query($sql);

        // Create promosi table
        $sql = "
        CREATE TABLE IF NOT EXISTS promosi (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            kode_promo TEXT NOT NULL UNIQUE,
            deskripsi TEXT,
            jenis_diskon TEXT NOT NULL, -- 'percentage' or 'fixed_amount'
            nilai_diskon REAL NOT NULL,
            tanggal_mulai DATETIME,
            tanggal_berakhir DATETIME,
            status TEXT DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->query($sql);

        // Create transaksi table
        $sql = "
        CREATE TABLE IF NOT EXISTS transaksi (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_pesanan INTEGER NOT NULL,
            metode_pembayaran TEXT NOT NULL,
            jumlah_pembayaran REAL NOT NULL,
            status_pembayaran TEXT NOT NULL DEFAULT 'pending',
            bukti_pembayaran TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_pesanan) REFERENCES pesanan(id)
        )";
        $db->query($sql);

        // Create trigger to update updated_at timestamp
        $triggerSql = "
        CREATE TRIGGER IF NOT EXISTS update_timestamp_trigger
        AFTER UPDATE ON users
        FOR EACH ROW
        BEGIN
            UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
        END;";
        $db->query($triggerSql);

        // Create trigger for produk table
        $triggerSql = "
        CREATE TRIGGER IF NOT EXISTS update_produk_timestamp_trigger
        AFTER UPDATE ON produk
        FOR EACH ROW
        BEGIN
            UPDATE produk SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
        END;";
        $db->query($triggerSql);

        // Create trigger for pesanan table
        $triggerSql = "
        CREATE TRIGGER IF NOT EXISTS update_pesanan_timestamp_trigger
        AFTER UPDATE ON pesanan
        FOR EACH ROW
        BEGIN
            UPDATE pesanan SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
        END;";
        $db->query($triggerSql);

        // Create indexes for better performance
        $db->query("CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)");
        $db->query("CREATE INDEX IF NOT EXISTS idx_produk_kategori ON produk(id_kategori)");
        $db->query("CREATE INDEX IF NOT EXISTS idx_pesanan_user ON pesanan(id_user)");
        $db->query("CREATE INDEX IF NOT EXISTS idx_pesanan_status ON pesanan(status_pesanan)");
        $db->query("CREATE INDEX IF NOT EXISTS idx_detail_pesanan_pesanan ON detail_pesanan(id_pesanan)");
        $db->query("CREATE INDEX IF NOT EXISTS idx_keranjang_user ON keranjang(id_user)");

        return true;
    } catch (Exception $e) {
        error_log('Migration failed: ' . $e->getMessage());
        return false;
    }
}
?>