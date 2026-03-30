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
        Schema::create('influencers', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // Kunci utama biar gak dobel
            $table->string('full_name')->nullable();
            $table->text('profile_picture')->nullable();
            $table->text('bio')->nullable();
            $table->string('niche')->nullable();
            $table->bigInteger('followers')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);

            // 🚨 INI KUNCI ASYNC KITA: Tandai data belum dianalisis AI
            $table->boolean('is_analyzed')->default(false);
            $table->text('ai_style_tags')->nullable(); // Nanti diisi sama Python

            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('influencers');
    }
};
