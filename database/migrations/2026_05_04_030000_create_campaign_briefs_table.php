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
        Schema::create('campaign_briefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->string('brand_name');
            $table->string('brief_name');
            $table->string('hashtags')->nullable();
            $table->string('tag_account')->nullable();
            $table->string('link_yellow_cart')->nullable();
            $table->date('draft_submission')->nullable();
            $table->date('draft_post')->nullable();
            $table->text('dos')->nullable();
            $table->text('donts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_briefs');
    }
};
