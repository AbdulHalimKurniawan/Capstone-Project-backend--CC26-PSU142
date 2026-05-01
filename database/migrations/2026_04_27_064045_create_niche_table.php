<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. TABEL KATEGORI UTAMA (Contoh: "Beauty & Fashion", "Gaming")
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Kategori Utama
            $table->timestamps();
        });

        // 2. TABEL SUB-NICHE (Contoh: "Skincare", "Mobile Gaming")
        Schema::create('niches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // Relasi ke Induk
            $table->string('name'); // Nama Sub-Niche
            $table->timestamps();
        });

        // 3. TABEL PIVOT (Jembatan antara Influencer dan Niche)
        // Karena 1 Influencer bisa punya banyak Niche, dan 1 Niche bisa dimiliki banyak Influencer
        Schema::create('influencer_niche', function (Blueprint $table) {
            $table->id();
            $table->foreignId('influencer_id')->constrained('influencers')->onDelete('cascade');
            $table->foreignId('niche_id')->constrained('niches')->onDelete('cascade');

            // Opsional: Buat nandain mana niche utama, mana niche sampingan
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('influencer_niche');
        Schema::dropIfExists('niches');
        Schema::dropIfExists('categories');
    }
};
