
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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Menambahkan unique agar tidak ada kategori dengan nama yang sama
            $table->string('slug')->unique(); // Slug untuk URL
            $table->string('image')->nullable(); // Gambar kategori
            $table->bigInteger('parent_id')->unsigned()->nullable(); // Tipe data bigInteger untuk parent_id
            $table->timestamps();
            
            // Menambahkan foreign key untuk parent_id
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
