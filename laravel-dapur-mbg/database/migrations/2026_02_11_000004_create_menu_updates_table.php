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
        Schema::create('menu_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_supplier')->constrained('users')->onDelete('cascade'); // Assuming Dapur user is a supplier
            $table->string('judul_update');
            $table->text('deskripsi_update');
            $table->json('produk_terupdate')->nullable(); // Store updated products as JSON
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
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
        Schema::dropIfExists('menu_updates');
    }
};