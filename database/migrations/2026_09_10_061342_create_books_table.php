<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_induk', 30)->unique();
            $table->string('judul', 200);
            $table->string('penulis', 100);
            $table->string('penerbit', 100);
            $table->year('tahun_terbit')->nullable();
            $table->integer('stok')->default(1);
            $table->integer('stok_total')->default(1);
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('rak', 20)->nullable();
            $table->string('cover', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};