<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Membuat tabel kategori produk
        Schema::create('kategori_produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori')->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Membuat tabel produk
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 10, 2);
            $table->integer('stok')->default(0);
            $table->foreignId('id_kategori')->constrained('kategori_produk')->onDelete('set null');
            $table->foreignId('id_supplier')->constrained('users')->onDelete('cascade');
            $table->string('gambar_produk')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Membuat tabel keranjang
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('produk')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->timestamps();
        });

        // Membuat tabel pesanan
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->decimal('total_harga', 10, 2);
            $table->enum('status_pesanan', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->string('metode_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamp('tanggal_pesan')->useCurrent();
            $table->date('tanggal_pengiriman')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Membuat tabel detail pesanan
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('produk')->onDelete('cascade');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // Membuat tabel pembayaran
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade');
            $table->decimal('jumlah_pembayaran', 10, 2);
            $table->string('metode_pembayaran');
            $table->enum('status_pembayaran', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('tanggal_pembayaran')->useCurrent();
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();
        });

        // Membuat tabel pelacakan pesanan
        Schema::create('pelacakan_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade');
            $table->enum('status_pesanan', ['order_placed', 'confirmed', 'processing', 'shipped', 'delivered']);
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_update')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pelacakan_pesanan');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('keranjang');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori_produk');
    }
};