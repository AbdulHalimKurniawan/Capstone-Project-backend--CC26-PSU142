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
        Schema::create('campaign_strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->string('strategy_name');
            $table->string('tagline')->nullable();
            $table->json('ig_deliverables')->nullable();
            $table->json('tiktok_deliverables')->nullable();
            $table->bigInteger('est_reach')->nullable();
            $table->string('est_sales_range')->nullable();
            $table->decimal('est_roi', 8, 2)->nullable();
            $table->json('addons')->nullable();
            $table->json('selected_influencers')->nullable();
            
            // Field baru untuk tahap 2 "Yang di Dapat"
            $table->string('allocation_text')->nullable();
            $table->bigInteger('fee_influencer')->nullable();

            $table->boolean('is_selected')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_strategies');
    }
};
