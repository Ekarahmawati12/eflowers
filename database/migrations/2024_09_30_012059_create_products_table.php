<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama produk (bunga)
            $table->string('slug')->unique(); // Slug untuk URL
            $table->string('short_description')->nullable(); // Deskripsi singkat bunga
            $table->text('description'); // Deskripsi detail tentang bunga
            $table->decimal('regular_price', 8, 2); // Harga bunga
            $table->enum('stock_status', ['instock', 'outofstock']); // Status stok (tersedia atau tidak)
            $table->boolean('featured')->default(false); // Produk unggulan
            $table->unsignedInteger('quantity')->default(10); // Jumlah stok bunga
            $table->string('image')->nullable(); // Gambar utama bunga
            $table->text('images')->nullable(); // Galeri gambar produk
            $table->unsignedBigInteger('category_id')->nullable(); // Jenis bunga (misalnya mawar, anggrek, dll.)            
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
