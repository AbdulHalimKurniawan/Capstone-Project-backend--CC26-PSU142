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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('influencer_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['instagram', 'tiktok']); // Menentukan jenis sosmed
            $table->string('username');
            $table->text('profile_picture')->nullable();
            $table->text('bio')->nullable();
            $table->bigInteger('followers')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->integer('average_views')->default(0);
            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();
            $table->unique(['platform', 'username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
