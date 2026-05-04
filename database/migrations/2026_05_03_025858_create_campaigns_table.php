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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('niche_id')->constrained('niches')->onDelete('cascade');
            $table->bigInteger('budget');
            $table->json('platforms');
            
            // Kolom baru sesuai analisis Frontend
            $table->text('product_description')->nullable();
            $table->integer('target_age_min')->nullable();
            $table->integer('target_age_max')->nullable();
            $table->string('target_location')->nullable();
            $table->string('target_gender')->nullable();
            $table->string('campaign_objective')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
