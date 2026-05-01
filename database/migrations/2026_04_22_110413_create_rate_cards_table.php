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
        Schema::create('rate_cards', function (Blueprint $table) {
            $table->id();
            // Relasi langsung ke Akun Sosmed (Bukan ke Jantung/Influencer lagi)
            $table->foreignId('social_account_id')->constrained('social_accounts')->onDelete('cascade');

            // Harga Universal (Berlaku untuk IG maupun TikTok)
            $table->bigInteger('base_rate')->default(0);     // 100% (Reels IG / Video TikTok)
            $table->bigInteger('story_rate')->default(0);    // 25% - 30% (Story IG/TikTok)
            $table->bigInteger('post_rate')->default(0);     // 70% - 80% (Feed IG / Carousel TikTok)
            $table->bigInteger('pp_rate')->default(0);       // 15% - 20% (Paid Promote)

            // Add-ons Universal
            $table->bigInteger('addon_owning')->default(0);  // 50%
            $table->bigInteger('addon_boost')->default(0);   // 30%
            $table->bigInteger('addon_link')->default(0);    // 40% - 50% (Yellow Cart)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_cards');
    }
};
