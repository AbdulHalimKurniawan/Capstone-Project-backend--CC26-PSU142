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
            $table->string('full_name')->nullable();
            $table->string('niche')->nullable();
            $table->integer('influencer_age')->nullable();
            $table->string('influencer_location')->nullable();
            $table->boolean('is_analyzed')->default(false);
            $table->json('embedding_vector')->nullable();
            $table->integer('audience_min_age')->nullable();
            $table->integer('audience_max_age')->nullable();
            $table->string('audience_gender')->nullable();
            $table->string('audience_location')->nullable();
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
